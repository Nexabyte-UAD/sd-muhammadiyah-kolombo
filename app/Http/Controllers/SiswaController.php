<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ekstrakurikuler;
use App\Models\Kelas;
use App\Models\RiwayatAkademik;
use App\Models\Siswa;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Controller SiswaController
 * 
 * Mengelola seluruh fungsionalitas CRUD data siswa, pengelolaan alumni,
 * proses ekspor laporan CSV, serta pemrosesan kenaikan/kelulusan siswa secara massal.
 */
class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa untuk panel administrator.
     * Mendukung pemfilteran status siswa (aktif, alumni, keluar, arsip/terhapus),
     * filter berdasarkan kelas, pencarian teks (nama/NIS), dan pengaturan jumlah baris per halaman.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mendapatkan filter status, kelas, pencarian, dan limit pagination dari request query
        $status = $request->query('status', 'aktif');
        $kelas = $request->query('kelas');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Tentukan query dasar apakah dari data terhapus (arsip) atau data normal
        $query = ($status === 'arsip' ? Siswa::onlyTrashed() : Siswa::query())
            ->with('kelasData');

        // Menerapkan filter query berdasarkan status siswa
        if ($status === 'arsip') {
            // Arsip mencakup seluruh status siswa yang pernah dihapus soft delete.
        } elseif ($status === 'alumni') {
            $query->alumni();
        } elseif ($status === 'keluar') {
            $query->keluar();
        } else {
            $status = 'aktif';
            $query->aktif();
        }

        // Filter berdasarkan tingkat kelas jika parameter disediakan
        if ($kelas && Kelas::where('tingkat', $kelas)->exists()) {
            $query->kelas($kelas);
        }

        // Filter berdasarkan pencarian nama atau NIS siswa
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Dapatkan data siswa terurut abjad secara terpaginasi
        $siswas = $query->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();

        // Dapatkan daftar kelas untuk menu drop-down filter kelas
        $daftarKelas = $this->daftarKelas();

        return view('admin.siswa.index', compact('siswas', 'status', 'kelas', 'search', 'daftarKelas', 'perPage'));
    }

    /**
     * Menampilkan daftar alumni secara terpisah di menu Tracer Study/Alumni.
     * Mendukung pemfilteran berdasarkan tahun lulus, pencarian kata kunci, dan pagination.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function alumniIndex(Request $request)
    {
        $tahunLulus = $request->query('tahun_lulus');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        // Query khusus siswa berstatus alumni beserta riwayat pendidikan lanjutannya
        $query = Siswa::alumni()->with('riwayatPendidikan');

        if ($tahunLulus) {
            $query->where('tahun_lulus', $tahunLulus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $alumni = $query->orderBy('tahun_lulus', 'desc')->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();

        // Mengambil daftar unik tahun lulus untuk dropdown filter tahun lulus
        $daftarTahunLulus = Siswa::alumni()
            ->whereNotNull('tahun_lulus')
            ->selectRaw('DISTINCT tahun_lulus')
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        return view('admin.alumni.index', compact('alumni', 'tahunLulus', 'search', 'perPage', 'daftarTahunLulus'));
    }

    /**
     * Menampilkan halaman formulir tambah data siswa baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.siswa.create', [
            'daftarKelas' => $this->daftarKelas(),
            'daftarEkstrakurikuler' => $this->daftarEkstrakurikuler(),
        ]);
    }

    /**
     * Menyimpan data siswa baru ke database.
     * Melakukan validasi input, auto-formatting input teks, pengecekan kapasitas kelas,
     * penyimpanan file foto, sinkronisasi ekskul & riwayat alumni, dan pembuatan log audit.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        // Validasi input data siswa dan relasinya
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('siswas', 'nis')],
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'alamat' => 'nullable|string',
            'kelas' => [
                'nullable',
                'required_if:status,aktif',
                Rule::exists('kelas', 'tingkat'),
            ],
            'status' => 'required|in:aktif,alumni,keluar',
            'tahun_masuk' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'tahun_lulus' => 'nullable|required_if:status,alumni|integer|min:2000|max:'.(date('Y') + 5),
            'tanggal_keluar' => 'nullable|required_if:status,keluar|date',
            'sekolah_tujuan' => 'nullable|required_if:status,keluar|string|max:255',
            'alasan_keluar' => 'nullable|string|max:1000',
            'pendidikan' => ['nullable', 'array'],
            'pendidikan.*.jenjang' => ['nullable', 'required_with:pendidikan.*.institusi,pendidikan.*.jurusan,pendidikan.*.tahun_masuk,pendidikan.*.tahun_selesai', 'string', 'max:50'],
            'pendidikan.*.institusi' => ['nullable', 'required_with:pendidikan.*.jenjang,pendidikan.*.jurusan,pendidikan.*.tahun_masuk,pendidikan.*.tahun_selesai', 'string', 'max:255'],
            'pendidikan.*.jurusan' => ['nullable', 'string', 'max:255'],
            'pendidikan.*.tahun_masuk' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            'pendidikan.*.tahun_selesai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],
            'pekerjaan_alumni' => ['nullable', 'array'],
            'pekerjaan_alumni.*.pekerjaan' => ['nullable', 'required_with:pekerjaan_alumni.*.perusahaan,pekerjaan_alumni.*.tahun_mulai,pekerjaan_alumni.*.tahun_selesai', 'string', 'max:255'],
            'pekerjaan_alumni.*.perusahaan' => ['nullable', 'string', 'max:255'],
            'pekerjaan_alumni.*.tahun_mulai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            'pekerjaan_alumni.*.tahun_selesai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ekstrakurikuler_ids' => ['nullable', 'array'],
            'ekstrakurikuler_ids.*' => [Rule::exists('ekstrakurikulers', 'id')],
        ]);

        // Memvalidasi kronologi tahun lahir terhadap tahun masuk sekolah atau selesai pendidikan alumni
        $this->validateKronologiRiwayat($request);
        $data = $request->except(['foto', 'ekstrakurikuler_ids', 'pendidikan', 'pekerjaan_alumni']);
        
        // Memformat penulisan nama, alamat, tempat lahir agar mengikuti kaidah standar Indonesia
        $data = $formatter->fields($data, [
            'nama' => 'name',
            'tempat_lahir' => 'title',
            'alamat' => 'address',
            'sekolah_tujuan' => 'title',
            'alasan_keluar' => 'sentence',
        ]);

        // Penyesuaian data atribut berdasarkan status siswa
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
            $data['kelas_id'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } elseif ($data['status'] === 'aktif') {
            $data['kelas_id'] = Kelas::where('tingkat', $data['kelas'])->value('id');
            // Pastikan kapasitas kelas yang dituju belum penuh sebelum mendaftarkan siswa aktif
            $this->pastikanKapasitasKelas($data['kelas_id']);
            $data['tahun_lulus'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } else {
            // Status Keluar
            $data['kelas'] = $data['kelas_id'] = $data['tahun_lulus'] = null;
        }

        // Upload foto baru jika ada
        $fotoBaru = $request->hasFile('foto')
            ? $request->file('foto')->store('siswa', 'public')
            : null;
        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        try {
            // Jalankan transaksi database agar konsistensi data terjaga
            DB::transaction(function () use ($data, $request, $formatter) {
                $siswa = Siswa::create($data);
                $siswa->ekstrakurikulers()->sync($request->input('ekstrakurikuler_ids', []));
                $this->syncRiwayatAlumni($siswa, $request, $formatter);

                // Mencatat aktivitas penambahan ke audit log
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'Tambah',
                    'module' => 'Siswa',
                    'description' => 'Menambahkan siswa baru: '.$data['nama']
                        .($data['status'] === 'alumni' ? ' (Alumni)' : ' ('.$data['kelas'].')'),
                ]);
            });
        } catch (\Throwable $exception) {
            // Hapus file foto yang baru diunggah jika transaksi gagal
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }
            throw $exception;
        }

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil ditambahkan');
    }

    /**
     * Menampilkan halaman formulir edit data siswa.
     * 
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\View\View
     */
    public function edit(Siswa $siswa)
    {
        return view('admin.siswa.edit', [
            'siswa' => $siswa,
            'daftarKelas' => $this->daftarKelas(),
            'daftarEkstrakurikuler' => $this->daftarEkstrakurikuler(),
        ]);
    }

    /**
     * Memperbarui data siswa di database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Siswa  $siswa
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Siswa $siswa, IndonesianTextFormatter $formatter)
    {
        if ($siswa->status === 'aktif' && $request->input('status') === 'alumni') {
            throw ValidationException::withMessages([
                'status' => 'Kelulusan siswa aktif harus diproses melalui menu Kenaikan Kelas agar riwayat akademik tercatat.',
            ]);
        }

        // Validasi input data siswa dan relasinya
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('siswas', 'nis')->ignore($siswa->id),
            ],
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'alamat' => 'nullable|string',
            'kelas' => [
                'nullable',
                'required_if:status,aktif',
                Rule::exists('kelas', 'tingkat'),
            ],
            'status' => 'required|in:aktif,alumni,keluar',
            'tahun_masuk' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'tahun_lulus' => 'nullable|required_if:status,alumni|integer|min:2000|max:'.(date('Y') + 5),
            'tanggal_keluar' => 'nullable|required_if:status,keluar|date',
            'sekolah_tujuan' => 'nullable|required_if:status,keluar|string|max:255',
            'alasan_keluar' => 'nullable|string|max:1000',
            'pendidikan' => ['nullable', 'array'],
            'pendidikan.*.jenjang' => ['nullable', 'required_with:pendidikan.*.institusi,pendidikan.*.jurusan,pendidikan.*.tahun_masuk,pendidikan.*.tahun_selesai', 'string', 'max:50'],
            'pendidikan.*.institusi' => ['nullable', 'required_with:pendidikan.*.jenjang,pendidikan.*.jurusan,pendidikan.*.tahun_masuk,pendidikan.*.tahun_selesai', 'string', 'max:255'],
            'pendidikan.*.jurusan' => ['nullable', 'string', 'max:255'],
            'pendidikan.*.tahun_masuk' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            'pendidikan.*.tahun_selesai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],
            'pekerjaan_alumni' => ['nullable', 'array'],
            'pekerjaan_alumni.*.pekerjaan' => ['nullable', 'required_with:pekerjaan_alumni.*.perusahaan,pekerjaan_alumni.*.tahun_mulai,pekerjaan_alumni.*.tahun_selesai', 'string', 'max:255'],
            'pekerjaan_alumni.*.perusahaan' => ['nullable', 'string', 'max:255'],
            'pekerjaan_alumni.*.tahun_mulai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 5)],
            'pekerjaan_alumni.*.tahun_selesai' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ekstrakurikuler_ids' => ['nullable', 'array'],
            'ekstrakurikuler_ids.*' => [Rule::exists('ekstrakurikulers', 'id')],
        ]);

        $this->validateKronologiRiwayat($request);
        $data = $request->except(['foto', 'ekstrakurikuler_ids', 'pendidikan', 'pekerjaan_alumni']);
        $data = $formatter->fields($data, [
            'nama' => 'name',
            'tempat_lahir' => 'title',
            'alamat' => 'address',
            'sekolah_tujuan' => 'title',
            'alasan_keluar' => 'sentence',
        ]);

        // Penyesuaian data atribut berdasarkan status siswa
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
            $data['kelas_id'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } elseif ($data['status'] === 'aktif') {
            $data['kelas_id'] = Kelas::where('tingkat', $data['kelas'])->value('id');
            // Pastikan kapasitas kelas tujuan mencukupi (mengabaikan kapasitas ID siswa yang sedang di-edit ini)
            $this->pastikanKapasitasKelas($data['kelas_id'], $siswa->id);
            $data['tahun_lulus'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } else {
            // Status Keluar
            $data['kelas'] = $data['kelas_id'] = $data['tahun_lulus'] = null;
        }

        $fotoLama = $siswa->foto;
        $fotoBaru = $request->hasFile('foto')
            ? $request->file('foto')->store('siswa', 'public')
            : null;
        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        try {
            DB::transaction(function () use ($siswa, $data, $request, $formatter) {
                $siswa->update($data);
                $siswa->ekstrakurikulers()->sync($request->input('ekstrakurikuler_ids', []));
                $this->syncRiwayatAlumni($siswa, $request, $formatter);

                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'Update',
                    'module' => 'Siswa',
                    'description' => 'Memperbarui biodata siswa: '.$data['nama'],
                ]);
            });
        } catch (\Throwable $exception) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }
            throw $exception;
        }

        // Hapus foto lama di storage jika upload foto baru sukses
        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil diupdate');
    }

    /**
     * Mengarsipkan data siswa (Soft Delete) dari sistem ke daftar arsip.
     * 
     * @param  \App\Models\Siswa  $siswa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Siswa $siswa)
    {
        $nama = $siswa->nama;
        $status = $siswa->status;
        $siswa->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Hapus',
            'module' => 'Siswa',
            'description' => 'Mengarsipkan data siswa: '.$nama,
        ]);

        return redirect()->route('admin.siswa.index', ['status' => $status])->with('success', 'Data siswa berhasil diarsipkan');
    }

    /**
     * Memulihkan data siswa yang sebelumnya diarsipkan (Restore Soft Delete).
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(int $id)
    {
        $siswa = Siswa::onlyTrashed()->findOrFail($id);
        $siswa->restore();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Pulihkan',
            'module' => 'Siswa',
            'description' => 'Memulihkan data siswa: '.$siswa->nama,
        ]);

        return redirect()->route('admin.siswa.index', ['status' => 'arsip'])
            ->with('success', 'Data siswa berhasil dipulihkan.');
    }

    /**
     * Mengekspor data siswa berdasarkan filter status saat ini ke format berkas CSV.
     * Dilengkapi mitigasi kerentanan CSV Injection.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export(Request $request)
    {
        $status = $request->query('status', 'aktif');
        $query = match ($status) {
            'alumni' => Siswa::alumni()->with(['riwayatPendidikan', 'riwayatPekerjaan']),
            'keluar' => Siswa::keluar(),
            'arsip' => Siswa::onlyTrashed(),
            default => Siswa::aktif()->with('kelasData'),
        };

        return response()->streamDownload(function () use ($query) {
            $file = fopen('php://output', 'w');
            // Menulis UTF-8 BOM untuk kompatibilitas karakter khusus di MS Excel
            fwrite($file, "\xEF\xBB\xBF");
            // Fungsi penangkal CSV Injection dengan menambahkan tanda petik satu pada karakter khusus
            $aman = static function ($value) {
                $value = (string) ($value ?? '');

                return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
            };
            
            // Header kolom CSV
            fputcsv($file, [
                'NIS', 'Nama', 'Jenis Kelamin', 'Agama', 'Kelas', 'Status',
                'Tahun Masuk', 'Tahun Lulus', 'Sekolah Tujuan/Pendidikan', 'Pekerjaan',
            ]);

            // Tulis baris data per chunk 500 untuk menghindari kehabisan memori RAM
            $query->orderBy('nama')->chunk(500, function ($siswas) use ($file, $aman) {
                foreach ($siswas as $siswa) {
                    fputcsv($file, array_map($aman, [
                        $siswa->nis,
                        $siswa->nama,
                        $siswa->jenis_kelamin,
                        $siswa->agama,
                        $siswa->kelasData?->tingkat ?? $siswa->kelas,
                        $siswa->status,
                        $siswa->tahun_masuk,
                        $siswa->tahun_lulus,
                        $siswa->sekolah_tujuan
                            ?: $siswa->riwayatPendidikan?->pluck('institusi')->join('; '),
                        $siswa->riwayatPekerjaan?->pluck('pekerjaan')->join('; '),
                    ]));
                }
            });
            fclose($file);
        }, 'data-siswa-'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Mengekspor khusus data alumni ke format berkas CSV.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function alumniExport(Request $request)
    {
        $query = Siswa::alumni()->with('riwayatPendidikan');

        if ($tahunLulus = $request->query('tahun_lulus')) {
            $query->where('tahun_lulus', $tahunLulus);
        }

        return response()->streamDownload(function () use ($query) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");
            $aman = static function ($value) {
                $value = (string) ($value ?? '');

                return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
            };
            
            // Header kolom khusus alumni
            fputcsv($file, [
                'NIS', 'Nama', 'Jenis Kelamin', 'Tahun Masuk', 'Tahun Lulus', 'Pendidikan Lanjutan',
            ]);

            $query->orderBy('tahun_lulus', 'desc')->orderBy('nama')->chunk(500, function ($alumni) use ($file, $aman) {
                foreach ($alumni as $siswa) {
                    $pendidikan = $siswa->riwayatPendidikan
                        ->map(fn ($p) => implode(' ', array_filter([$p->jenjang, $p->institusi, $p->jurusan ? "({$p->jurusan})" : ''])))
                        ->join('; ');

                    fputcsv($file, array_map($aman, [
                        $siswa->nis,
                        $siswa->nama,
                        $siswa->jenis_kelamin,
                        $siswa->tahun_masuk,
                        $siswa->tahun_lulus,
                        $pendidikan ?: '-',
                    ]));
                }
            });
            fclose($file);
        }, 'data-alumni-'.date('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Menampilkan dasbor panel pemrosesan kenaikan kelas atau kelulusan siswa secara kolektif.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function promotePage(Request $request)
    {
        $daftarKelas = $this->daftarKelas();
        $kelasAsal = $request->query('kelas');
        $tahunAjaran = $request->query('tahun_ajaran', $this->tahunAjaranAktif());
        $siswas = collect();
        $kelasTujuanNaik = collect();
        $kelasAsalTerakhir = false;

        // Mengambil daftar siswa aktif berdasarkan kelas asal yang disaring
        if ($kelasAsal && $daftarKelas->contains('tingkat', $kelasAsal)) {
            $kelasAsalModel = $daftarKelas->firstWhere('tingkat', $kelasAsal);
            $tingkatBerikutnya = $this->tingkatBerikutnya($kelasAsalModel, $daftarKelas);
            $kelasTujuanNaik = $tingkatBerikutnya === null
                ? collect()
                : $daftarKelas->filter(
                    fn (Kelas $kelas) => $this->peringkatKelas($kelas) === $tingkatBerikutnya
                )->values();
            $kelasAsalTerakhir = $tingkatBerikutnya === null
                && $this->peringkatKelas($kelasAsalModel) === 6;

            $siswas = Siswa::aktif()
                ->kelas($kelasAsal)
                ->orderBy('nama')
                ->get();
        }
        
        // Riwayat pemrosesan akademik 20 terakhir untuk tabel riwayat log di bagian bawah halaman
        $riwayat = RiwayatAkademik::with(['siswa', 'pemroses'])
            ->latest('tanggal_proses')
            ->take(20)
            ->get();

        return view('admin.siswa.promote', compact(
            'daftarKelas',
            'kelasAsal',
            'tahunAjaran',
            'siswas',
            'riwayat',
            'kelasTujuanNaik',
            'kelasAsalTerakhir'
        ));
    }

    /**
     * Memproses keputusan kenaikan kelas, tinggal kelas, kelulusan, atau pindah sekolah siswa secara massal.
     * Mencatat detail keputusan ke tabel Riwayat Akademik.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function promote(Request $request)
    {
        // Validasi struktur array input keputusan
        $validated = $request->validate([
            'kelas_asal' => ['required', Rule::exists('kelas', 'tingkat')],
            'tahun_ajaran' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'keputusan' => ['required', 'array', 'min:1'],
            'keputusan.*.status' => ['required', Rule::in(['naik', 'tinggal', 'lulus', 'pindah'])],
            'keputusan.*.kelas_tujuan' => ['nullable', Rule::exists('kelas', 'tingkat')],
            'keputusan.*.catatan' => ['nullable', 'string', 'max:500'],
            'keputusan.*.sekolah_tujuan' => ['nullable', 'string', 'max:255'],
            'keputusan.*.tanggal_keluar' => ['nullable', 'date'],
        ]);
        
        // Memastikan format tahun ajaran berurutan (misal: 2024/2025)
        [$awalTahunAjaran, $akhirTahunAjaran] = array_map('intval', explode('/', $validated['tahun_ajaran']));
        if ($akhirTahunAjaran !== $awalTahunAjaran + 1) {
            throw ValidationException::withMessages([
                'tahun_ajaran' => 'Tahun ajaran harus berurutan, contoh: 2026/2027.',
            ]);
        }

        $studentIds = array_map('intval', array_keys($validated['keputusan']));
        $kelasAsalModel = Kelas::where('tingkat', $validated['kelas_asal'])->firstOrFail();
        $daftarKelas = $this->daftarKelas();
        $peringkatKelasAsal = $this->peringkatKelas($kelasAsalModel);
        if ($peringkatKelasAsal === null) {
            throw ValidationException::withMessages([
                'kelas_asal' => 'Urutan kelas asal belum dapat dikenali. Isi kolom Urutan Kelas terlebih dahulu.',
            ]);
        }
        $tingkatBerikutnya = $this->tingkatBerikutnya($kelasAsalModel, $daftarKelas);
        $kelasTujuanValid = $tingkatBerikutnya === null
            ? collect()
            : $daftarKelas->filter(
                fn (Kelas $kelas) => $this->peringkatKelas($kelas) === $tingkatBerikutnya
            )->pluck('tingkat');

        $siswas = Siswa::aktif()
            ->where('kelas', $validated['kelas_asal'])
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        if ($siswas->count() !== count($studentIds)) {
            throw ValidationException::withMessages([
                'keputusan' => 'Terdapat siswa yang bukan anggota kelas asal atau sudah tidak aktif.',
            ]);
        }

        $seluruhSiswaIds = Siswa::aktif()
            ->where('kelas', $validated['kelas_asal'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values();

        if ($seluruhSiswaIds->all() !== collect($studentIds)->sort()->values()->all()) {
            throw ValidationException::withMessages([
                'keputusan' => 'Keputusan harus ditentukan untuk seluruh siswa aktif di kelas asal.',
            ]);
        }

        if (RiwayatAkademik::where('tahun_ajaran', $validated['tahun_ajaran'])
            ->whereIn('siswa_id', $studentIds)
            ->exists()) {
            throw ValidationException::withMessages([
                'keputusan' => 'Sebagian siswa sudah diproses pada tahun ajaran ini. Riwayat tidak boleh ditimpa.',
            ]);
        }

        // Validasi logika bisnis setiap keputusan siswa
        foreach ($validated['keputusan'] as $siswaId => $item) {
            if ($item['status'] === 'naik' && empty($item['kelas_tujuan'])) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.kelas_tujuan" => 'Kelas tujuan wajib dipilih untuk siswa yang naik kelas.',
                ]);
            }

            if ($item['status'] === 'naik' && $tingkatBerikutnya === null) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.status" => $peringkatKelasAsal === 6
                        ? 'Siswa kelas tingkat akhir tidak dapat dinaikkan lagi. Pilih Lulus, Tinggal Kelas, atau Keluar.'
                        : 'Kelas satu tingkat di atas belum tersedia. Lengkapi data kelas terlebih dahulu.',
                ]);
            }

            if ($item['status'] === 'naik' && ! $kelasTujuanValid->contains($item['kelas_tujuan'])) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.kelas_tujuan" => 'Kelas tujuan harus berada tepat satu tingkat di atas kelas asal.',
                ]);
            }

            if ($item['status'] === 'lulus' && ($peringkatKelasAsal !== 6 || $tingkatBerikutnya !== null)) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.status" => 'Kelulusan hanya dapat dipilih untuk siswa kelas tingkat akhir.',
                ]);
            }

            if ($item['status'] === 'pindah' && empty($item['sekolah_tujuan'])) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.sekolah_tujuan" => 'Sekolah tujuan wajib diisi untuk siswa pindah.',
                ]);
            }
        }

        // Pengecekan kapasitas kelas tujuan agar tidak overload saat kenaikan kelas massal
        foreach (collect($validated['keputusan'])->where('status', 'naik')->groupBy('kelas_tujuan') as $tujuan => $items) {
            $kelasTujuan = Kelas::where('tingkat', $tujuan)->firstOrFail();
            $masihBelumDiproses = Siswa::aktif()
                ->kelas($tujuan)
                ->whereDoesntHave('riwayatAkademik', fn ($query) => $query
                    ->where('tahun_ajaran', $validated['tahun_ajaran']))
                ->exists();

            if ($masihBelumDiproses) {
                throw ValidationException::withMessages([
                    'keputusan' => "Proses status akhir tahun {$tujuan} terlebih dahulu sebelum menaikkan siswa ke kelas tersebut.",
                ]);
            }

            if ($kelasTujuan->kapasitas && $kelasTujuan->siswas()->where('status', 'aktif')->count() + $items->count() > $kelasTujuan->kapasitas) {
                throw ValidationException::withMessages([
                    'keputusan' => "Kapasitas {$kelasTujuan->tingkat} tidak mencukupi.",
                ]);
            }
        }

        // Proses perubahan massal di dalam Transaksi Database
        DB::transaction(function () use ($validated, $siswas) {
            foreach ($validated['keputusan'] as $siswaId => $item) {
                $siswa = $siswas->get((int) $siswaId);
                $kelasTujuan = $item['status'] === 'naik'
                    ? $item['kelas_tujuan']
                    : ($item['status'] === 'tinggal' ? $validated['kelas_asal'] : null);
                $kelasTujuanId = $kelasTujuan
                    ? Kelas::where('tingkat', $kelasTujuan)->value('id')
                    : null;

                // Catat perubahan ke riwayat akademik
                RiwayatAkademik::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $validated['tahun_ajaran'],
                        'kelas_asal' => $validated['kelas_asal'],
                        'kelas_tujuan' => $kelasTujuan,
                        'keputusan' => $item['status'],
                        'catatan' => $item['catatan'] ?? null,
                        'diproses_oleh' => auth()->id(),
                        'tanggal_proses' => now(),
                ]);

                // Perbarui biodata aktif siswa bersangkutan
                $siswa->update(match ($item['status']) {
                    'naik' => [
                        'kelas' => $kelasTujuan,
                        'kelas_id' => $kelasTujuanId,
                        'status' => 'aktif',
                        'tahun_lulus' => null,
                    ],
                    'tinggal' => [
                        'kelas' => $validated['kelas_asal'],
                        'kelas_id' => $kelasTujuanId,
                        'status' => 'aktif',
                        'tahun_lulus' => null,
                    ],
                    'lulus' => [
                        'kelas' => null,
                        'kelas_id' => null,
                        'status' => 'alumni',
                        'tahun_lulus' => (int) substr($validated['tahun_ajaran'], -4),
                    ],
                    'pindah' => [
                        'kelas' => null,
                        'kelas_id' => null,
                        'status' => 'keluar',
                        'tahun_lulus' => null,
                        'tanggal_keluar' => $item['tanggal_keluar'] ?? now()->toDateString(),
                        'sekolah_tujuan' => $item['sekolah_tujuan'],
                        'alasan_keluar' => $item['catatan'] ?? null,
                    ],
                });
            }
        });

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Update',
            'module' => 'Siswa',
            'description' => 'Memproses status akhir tahun '.$validated['tahun_ajaran']
                .' untuk kelas '.$validated['kelas_asal'],
        ]);

        return redirect()
            ->route('admin.siswa.promote.page', ['tahun_ajaran' => $validated['tahun_ajaran']])
            ->with('success', 'Status akhir tahun siswa berhasil diproses dan dicatat dalam riwayat.');
    }

    /**
     * Mendapatkan daftar kelas diurutkan berdasarkan urutan/tingkatan.
     */
    private function daftarKelas()
    {
        return Kelas::orderByRaw('urutan IS NULL')->orderBy('urutan')->orderBy('tingkat')->get();
    }

    /**
     * Menentukan tingkat akademik dari angka pada label kelas atau urutan sebagai cadangan.
     */
    private function peringkatKelas(Kelas $kelas): ?int
    {
        if (preg_match('/\d+/', $kelas->tingkat, $match)) {
            return (int) $match[0];
        }

        return $kelas->urutan !== null ? (int) $kelas->urutan : null;
    }

    /**
     * Mengambil tepat satu tingkat di atas kelas asal jika sudah dikonfigurasi.
     */
    private function tingkatBerikutnya(Kelas $kelasAsal, $daftarKelas): ?int
    {
        $peringkatAsal = $this->peringkatKelas($kelasAsal);

        if ($peringkatAsal === null) {
            return null;
        }

        $tingkatTujuan = $peringkatAsal + 1;

        return $daftarKelas
            ->map(fn (Kelas $kelas) => $this->peringkatKelas($kelas))
            ->contains($tingkatTujuan)
                ? $tingkatTujuan
                : null;
    }

    /**
     * Mendapatkan daftar seluruh ekstrakurikuler sekolah.
     */
    private function daftarEkstrakurikuler()
    {
        return Ekstrakurikuler::orderBy('nama')->get();
    }

    /**
     * Mendapatkan string representasi tahun ajaran yang sedang aktif secara dinamis berdasarkan bulan saat ini.
     * Bulan Juli ke atas masuk ke tahun ajaran baru.
     */
    private function tahunAjaranAktif(): string
    {
        $tahun = (int) date('Y');

        return (int) date('n') >= 7
            ? "{$tahun}/".($tahun + 1)
            : ($tahun - 1)."/{$tahun}";
    }

    /**
     * Menyinkronkan data riwayat pendidikan & pekerjaan alumni.
     */
    private function syncRiwayatAlumni(
        Siswa $siswa,
        Request $request,
        IndonesianTextFormatter $formatter
    ): void {
        if ($request->input('status') !== 'alumni') {
            return;
        }

        // Bersihkan data riwayat lama agar tidak duplikat saat update
        $siswa->riwayatPendidikan()->delete();
        $siswa->riwayatPekerjaan()->delete();

        // Simpan pendidikan alumni baru
        $pendidikan = collect($request->input('pendidikan', []))
            ->filter(fn ($item) => ! empty($item['jenjang']) && ! empty($item['institusi']))
            ->map(fn ($item) => $formatter->fields($item, [
                'jenjang' => 'title',
                'institusi' => 'title',
                'jurusan' => 'title',
            ]))
            ->values()->all();
            
        // Simpan pekerjaan alumni baru
        $pekerjaan = collect($request->input('pekerjaan_alumni', []))
            ->filter(fn ($item) => ! empty($item['pekerjaan']))
            ->map(fn ($item) => $formatter->fields($item, [
                'pekerjaan' => 'title',
                'perusahaan' => 'title',
            ]))
            ->values()->all();

        $siswa->riwayatPendidikan()->createMany($pendidikan);
        $siswa->riwayatPekerjaan()->createMany($pekerjaan);
    }

    /**
     * Validasi logika bisnis kronologi tahun (tahun masuk sekolah tidak boleh mustahil terhadap tahun kelahiran).
     */
    private function validateKronologiRiwayat(Request $request): void
    {
        if ($request->filled('tanggal_lahir') && $request->filled('tahun_masuk')) {
            $usiaMasuk = (int) $request->input('tahun_masuk')
                - (int) date('Y', strtotime($request->input('tanggal_lahir')));
            if ($usiaMasuk < 4 || $usiaMasuk > 15) {
                throw ValidationException::withMessages([
                    'tahun_masuk' => 'Tahun masuk tidak sesuai dengan usia siswa (rentang wajar 4–15 tahun).',
                ]);
            }
        }

        foreach ($request->input('pendidikan', []) as $index => $item) {
            if (! empty($item['tahun_masuk']) && ! empty($item['tahun_selesai'])
                && (int) $item['tahun_selesai'] < (int) $item['tahun_masuk']) {
                throw ValidationException::withMessages([
                    "pendidikan.{$index}.tahun_selesai" => 'Tahun selesai tidak boleh lebih awal dari tahun masuk.',
                ]);
            }
        }

        foreach ($request->input('pekerjaan_alumni', []) as $index => $item) {
            if (! empty($item['tahun_mulai']) && ! empty($item['tahun_selesai'])
                && (int) $item['tahun_selesai'] < (int) $item['tahun_mulai']) {
                throw ValidationException::withMessages([
                    "pekerjaan_alumni.{$index}.tahun_selesai" => 'Tahun selesai tidak boleh lebih awal dari tahun mulai.',
                ]);
            }
        }
    }

    /**
     * Memastikan kapasitas kelas belum penuh sebelum menambahkan siswa baru.
     */
    private function pastikanKapasitasKelas(?int $kelasId, ?int $abaikanSiswaId = null): void
    {
        $kelas = Kelas::find($kelasId);
        if ($kelas?->sudahPenuh($abaikanSiswaId)) {
            throw ValidationException::withMessages([
                'kelas' => "Kapasitas {$kelas->tingkat} sudah penuh.",
            ]);
        }
    }
}
