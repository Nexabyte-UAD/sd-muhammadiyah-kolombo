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

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'aktif');
        $kelas = $request->query('kelas');
        $search = $request->query('search');

        $query = ($status === 'arsip' ? Siswa::onlyTrashed() : Siswa::query())
            ->with('kelasData');

        if ($status === 'arsip') {
            // Arsip mencakup seluruh status siswa yang pernah dihapus.
        } elseif ($status === 'alumni') {
            $query->alumni();
        } elseif ($status === 'keluar') {
            $query->keluar();
        } else {
            $status = 'aktif';
            $query->aktif();
        }

        if ($kelas && Kelas::where('tingkat', $kelas)->exists()) {
            $query->kelas($kelas);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswas = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        $daftarKelas = $this->daftarKelas();

        return view('admin.siswa.index', compact('siswas', 'status', 'kelas', 'search', 'daftarKelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.siswa.create', [
            'daftarKelas' => $this->daftarKelas(),
            'daftarEkstrakurikuler' => $this->daftarEkstrakurikuler(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
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

        $this->validateKronologiRiwayat($request);
        $data = $request->except(['foto', 'ekstrakurikuler_ids', 'pendidikan', 'pekerjaan_alumni']);
        $data = $formatter->fields($data, [
            'nama' => 'name',
            'tempat_lahir' => 'title',
            'alamat' => 'address',
            'sekolah_tujuan' => 'title',
            'alasan_keluar' => 'sentence',
        ]);

        // Clean up kelas if alumni
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
            $data['kelas_id'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } elseif ($data['status'] === 'aktif') {
            $data['kelas_id'] = Kelas::where('tingkat', $data['kelas'])->value('id');
            $this->pastikanKapasitasKelas($data['kelas_id']);
            $data['tahun_lulus'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } else {
            $data['kelas'] = $data['kelas_id'] = $data['tahun_lulus'] = null;
        }

        $fotoBaru = $request->hasFile('foto')
            ? $request->file('foto')->store('siswa', 'public')
            : null;
        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        try {
            DB::transaction(function () use ($data, $request, $formatter) {
                $siswa = Siswa::create($data);
                $siswa->ekstrakurikulers()->sync($request->input('ekstrakurikuler_ids', []));
                $this->syncRiwayatAlumni($siswa, $request, $formatter);

                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'Tambah',
                    'module' => 'Siswa',
                    'description' => 'Menambahkan siswa baru: '.$data['nama']
                        .($data['status'] === 'alumni' ? ' (Alumni)' : ' ('.$data['kelas'].')'),
                ]);
            });
        } catch (\Throwable $exception) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }
            throw $exception;
        }

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa, IndonesianTextFormatter $formatter)
    {
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

        // Clean up kelas if alumni
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
            $data['kelas_id'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } elseif ($data['status'] === 'aktif') {
            $data['kelas_id'] = Kelas::where('tingkat', $data['kelas'])->value('id');
            $this->pastikanKapasitasKelas($data['kelas_id'], $siswa->id);
            $data['tahun_lulus'] = null;
            $data['tanggal_keluar'] = $data['sekolah_tujuan'] = $data['alasan_keluar'] = null;
        } else {
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

        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
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
            fwrite($file, "\xEF\xBB\xBF");
            $aman = static function ($value) {
                $value = (string) ($value ?? '');

                return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
            };
            fputcsv($file, [
                'NIS', 'Nama', 'Jenis Kelamin', 'Agama', 'Kelas', 'Status',
                'Tahun Masuk', 'Tahun Lulus', 'Sekolah Tujuan/Pendidikan', 'Pekerjaan',
            ]);

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
     * Show the promotion dashboard page.
     */
    public function promotePage(Request $request)
    {
        $daftarKelas = $this->daftarKelas();
        $kelasAsal = $request->query('kelas');
        $tahunAjaran = $request->query('tahun_ajaran', $this->tahunAjaranAktif());
        $siswas = collect();

        if ($kelasAsal && $daftarKelas->contains('tingkat', $kelasAsal)) {
            $siswas = Siswa::aktif()
                ->kelas($kelasAsal)
                ->orderBy('nama')
                ->get();
        }
        $riwayat = RiwayatAkademik::with(['siswa', 'pemroses'])
            ->latest('tanggal_proses')
            ->take(20)
            ->get();

        return view('admin.siswa.promote', compact(
            'daftarKelas',
            'kelasAsal',
            'tahunAjaran',
            'siswas',
            'riwayat'
        ));
    }

    /**
     * Process bulk promotion and graduation.
     */
    public function promote(Request $request)
    {
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
        [$awalTahunAjaran, $akhirTahunAjaran] = array_map('intval', explode('/', $validated['tahun_ajaran']));
        if ($akhirTahunAjaran !== $awalTahunAjaran + 1) {
            throw ValidationException::withMessages([
                'tahun_ajaran' => 'Tahun ajaran harus berurutan, contoh: 2026/2027.',
            ]);
        }

        $studentIds = array_map('intval', array_keys($validated['keputusan']));
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

        foreach ($validated['keputusan'] as $siswaId => $item) {
            if ($item['status'] === 'naik' && empty($item['kelas_tujuan'])) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.kelas_tujuan" => 'Kelas tujuan wajib dipilih untuk siswa yang naik kelas.',
                ]);
            }

            if ($item['status'] === 'naik' && $item['kelas_tujuan'] === $validated['kelas_asal']) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.kelas_tujuan" => 'Kelas tujuan harus berbeda dari kelas asal.',
                ]);
            }

            if ($item['status'] === 'pindah' && empty($item['sekolah_tujuan'])) {
                throw ValidationException::withMessages([
                    "keputusan.{$siswaId}.sekolah_tujuan" => 'Sekolah tujuan wajib diisi untuk siswa pindah.',
                ]);
            }
        }

        foreach (collect($validated['keputusan'])->where('status', 'naik')->groupBy('kelas_tujuan') as $tujuan => $items) {
            $kelasTujuan = Kelas::where('tingkat', $tujuan)->firstOrFail();
            if ($kelasTujuan->kapasitas && $kelasTujuan->siswas()->where('status', 'aktif')->count() + $items->count() > $kelasTujuan->kapasitas) {
                throw ValidationException::withMessages([
                    'keputusan' => "Kapasitas {$kelasTujuan->tingkat} tidak mencukupi.",
                ]);
            }
        }

        DB::transaction(function () use ($validated, $siswas) {
            foreach ($validated['keputusan'] as $siswaId => $item) {
                $siswa = $siswas->get((int) $siswaId);
                $kelasTujuan = $item['status'] === 'naik'
                    ? $item['kelas_tujuan']
                    : ($item['status'] === 'tinggal' ? $validated['kelas_asal'] : null);
                $kelasTujuanId = $kelasTujuan
                    ? Kelas::where('tingkat', $kelasTujuan)->value('id')
                    : null;

                RiwayatAkademik::updateOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran' => $validated['tahun_ajaran'],
                    ],
                    [
                        'kelas_asal' => $validated['kelas_asal'],
                        'kelas_tujuan' => $kelasTujuan,
                        'keputusan' => $item['status'],
                        'catatan' => $item['catatan'] ?? null,
                        'diproses_oleh' => auth()->id(),
                        'tanggal_proses' => now(),
                    ]
                );

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

    private function daftarKelas()
    {
        return Kelas::orderByRaw('urutan IS NULL')->orderBy('urutan')->orderBy('tingkat')->get();
    }

    private function daftarEkstrakurikuler()
    {
        return Ekstrakurikuler::orderBy('nama')->get();
    }

    private function tahunAjaranAktif(): string
    {
        $tahun = (int) date('Y');

        return (int) date('n') >= 7
            ? "{$tahun}/".($tahun + 1)
            : ($tahun - 1)."/{$tahun}";
    }

    private function syncRiwayatAlumni(
        Siswa $siswa,
        Request $request,
        IndonesianTextFormatter $formatter
    ): void {
        if ($request->input('status') !== 'alumni') {
            return;
        }

        $siswa->riwayatPendidikan()->delete();
        $siswa->riwayatPekerjaan()->delete();

        $pendidikan = collect($request->input('pendidikan', []))
            ->filter(fn ($item) => ! empty($item['jenjang']) && ! empty($item['institusi']))
            ->map(fn ($item) => $formatter->fields($item, [
                'jenjang' => 'title',
                'institusi' => 'title',
                'jurusan' => 'title',
            ]))
            ->values()->all();
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
