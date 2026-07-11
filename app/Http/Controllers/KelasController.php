<?php

namespace App\Http\Controllers;

use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Controller KelasController
 * 
 * Mengelola seluruh fungsionalitas CRUD data kelas, pembatasan kapasitas kelas,
 * penugasan Wali Kelas, serta sinkronisasi penamaan kelas ke tabel siswa.
 */
class KelasController extends Controller
{
    /**
     * Menampilkan daftar kelas.
     * Mendukung pencarian tingkat kelas, jurusan, atau nama wali kelas,
     * serta menghitung total siswa aktif di setiap kelas.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = $request->query('search');

        // Query relasi wali kelas dan hitung siswa berstatus aktif
        $query = Kelas::with('waliKelas')
            ->withCount(['siswas' => fn ($query) => $query->where('status', 'aktif')]);

        // Filter pencarian berdasarkan nama kelas atau wali kelas
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tingkat', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhereHas('waliKelas', function ($wq) use ($search) {
                      $wq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan kelas berdasarkan kolom 'urutan'
        $kelas = $query->orderByRaw('urutan IS NULL')
            ->orderBy('urutan')
            ->orderBy('tingkat')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.kelas.index', compact('kelas', 'perPage', 'search'));
    }

    /**
     * Menampilkan halaman formulir tambah kelas baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.kelas.create', [
            'gurus' => $this->gurus(),
        ]);
    }

    /**
     * Menyimpan data kelas baru ke database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Kelas::create($this->validated($request));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit kelas.
     * 
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\View\View
     */
    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', [
            'kelas' => $kelas,
            'gurus' => $this->gurus(),
        ]);
    }

    /**
     * Memperbarui data kelas di database.
     * Memastikan kapasitas kelas baru tidak lebih kecil dari jumlah siswa aktif di kelas tersebut,
     * serta melakukan sinkronisasi kolom string 'kelas' pada tabel siswa.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Kelas $kelas)
    {
        $data = $this->validated($request, $kelas);
        
        // Cek kapasitas agar tidak melanggar jumlah siswa aktif saat ini
        if (($data['kapasitas'] ?? null) && $kelas->siswas()->where('status', 'aktif')->count() > $data['kapasitas']) {
            throw ValidationException::withMessages([
                'kapasitas' => 'Kapasitas tidak boleh lebih kecil dari jumlah siswa aktif saat ini.',
            ]);
        }
        $kelas->update($data);
        
        // Sinkronisasi data string 'kelas' di tabel siswa agar tetap sinkron
        Siswa::withTrashed()->where('kelas_id', $kelas->id)->update(['kelas' => $kelas->tingkat]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus data kelas dari database.
     * Mencegah penghapusan jika kelas masih memiliki siswa aktif/arsip terikat.
     * 
     * @param  \App\Models\Kelas  $kelas
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Kelas $kelas)
    {
        // Cegah hapus kelas jika masih ada siswa
        if (Siswa::withTrashed()->where('kelas_id', $kelas->id)->exists()) {
            throw ValidationException::withMessages([
                'kelas' => 'Kelas tidak dapat dihapus karena masih memiliki siswa.',
            ]);
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    /**
     * Memvalidasi input formulir kelas.
     * Melakukan normalisasi format teks secara otomatis sebelum divalidasi.
     */
    private function validated(Request $request, ?Kelas $kelas = null): array
    {
        // Normalisasi format tingkat kelas dan program/jurusan kelas
        $request->merge([
            'tingkat' => Kelas::normalizeLabel($request->input('tingkat'), true),
            'jurusan' => Kelas::normalizeLabel($request->input('jurusan')),
        ]);

        $data = $request->validate([
            'tingkat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'tingkat')->ignore($kelas?->id),
            ],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:999'],
            'tahun_ajaran' => ['nullable', 'regex:/^\d{4}\/\d{4}$/'],
            'kapasitas' => ['nullable', 'integer', 'min:1', 'max:999'],
            'wali_kelas_id' => [
                'nullable',
                Rule::exists('guru_staffs', 'id')->where(
                    fn ($query) => $query->where('tipe', 'guru')
                ),
                // Seorang wali kelas hanya boleh memegang 1 kelas di tahun ajaran yang sama
                Rule::unique('kelas', 'wali_kelas_id')
                    ->where(fn ($query) => $query->where('tahun_ajaran', $request->input('tahun_ajaran')))
                    ->ignore($kelas?->id),
            ],
        ]);

        // Cek kronologi format tahun ajaran
        if (! empty($data['tahun_ajaran'])) {
            [$awal, $akhir] = array_map('intval', explode('/', $data['tahun_ajaran']));
            if ($akhir !== $awal + 1) {
                throw ValidationException::withMessages([
                    'tahun_ajaran' => 'Tahun ajaran harus berurutan, contoh: 2026/2027.',
                ]);
            }
        }

        return $data;
    }

    /**
     * Mendapatkan daftar pegawai dengan tipe 'guru' untuk dipilih sebagai Wali Kelas.
     */
    private function gurus()
    {
        return GuruStaff::where('tipe', 'guru')->orderBy('nama')->get();
    }
}
