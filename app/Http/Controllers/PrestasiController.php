<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Controller PrestasiController
 * 
 * Mengelola data prestasi/penghargaan siswa SD Muhammadiyah Komplek Kolombo,
 * termasuk pencarian, filter kategori prestasi, upload gambar bukti medali, dan log audit.
 */
class PrestasiController extends Controller
{
    /**
     * Menampilkan daftar prestasi siswa di panel admin.
     * Mendukung pencarian teks dan pemfilteran kategori (akademik, nonakademik, keagamaan).
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $kategori = (string) $request->query('kategori', '');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $query = Prestasi::query();

        // Cari berdasarkan judul, nama siswa, medali, atau penyelenggara
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('prestasi_medali', 'like', "%{$search}%")
                    ->orWhere('penyelenggara', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kategori prestasi
        if (array_key_exists($kategori, Prestasi::KATEGORI)) {
            $query->where('kategori', $kategori);
        }

        $prestasis = $query->orderBy('tanggal', 'desc')->paginate($perPage)->withQueryString();
        $kategoriPrestasi = Prestasi::KATEGORI;

        return view('admin.prestasi.index', compact('prestasis', 'kategoriPrestasi', 'search', 'kategori', 'perPage'));
    }

    /**
     * Menampilkan formulir tambah data prestasi baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $kategoriPrestasi = Prestasi::KATEGORI;
        $siswas = Siswa::orderBy('nama')->get();

        return view('admin.prestasi.create', compact('kategoriPrestasi', 'siswas'));
    }

    /**
     * Menyimpan data prestasi siswa baru ke database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => ['required', Rule::in(array_keys(Prestasi::KATEGORI))],
            'siswa_id' => ['required', Rule::exists('siswas', 'id')],
            'prestasi_medali' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'judul',
            'kategori',
            'siswa_id',
            'prestasi_medali',
            'penyelenggara',
            'deskripsi',
            'tanggal',
        ]);
        
        // Format teks input agar rapi
        $data = $formatter->fields($data, [
            'judul' => 'title',
            'prestasi_medali' => 'title',
            'penyelenggara' => 'title',
            'deskripsi' => 'sentence',
        ]);
        // Salin nama siswa secara otomatis berdasarkan siswa_id terpilih
        $data['nama_siswa'] = Siswa::findOrFail($data['siswa_id'])->nama;

        // Upload gambar piala/medali jika disertakan
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('prestasi', 'public');
        }

        Prestasi::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Tambah',
            'module' => 'Prestasi',
            'description' => 'Menambahkan prestasi: '.$data['judul'],
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil ditambahkan');
    }

    /**
     * Menampilkan formulir edit data prestasi.
     * 
     * @param  \App\Models\Prestasi  $prestasi
     * @return \Illuminate\View\View
     */
    public function edit(Prestasi $prestasi)
    {
        $kategoriPrestasi = Prestasi::KATEGORI;
        $siswas = Siswa::orderBy('nama')->get();

        return view('admin.prestasi.edit', compact('prestasi', 'kategoriPrestasi', 'siswas'));
    }

    /**
     * Memperbarui data prestasi siswa di database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Prestasi  $prestasi
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Prestasi $prestasi, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => ['required', Rule::in(array_keys(Prestasi::KATEGORI))],
            'siswa_id' => ['required', Rule::exists('siswas', 'id')],
            'prestasi_medali' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'judul',
            'kategori',
            'siswa_id',
            'prestasi_medali',
            'penyelenggara',
            'deskripsi',
            'tanggal',
        ]);
        $data = $formatter->fields($data, [
            'judul' => 'title',
            'prestasi_medali' => 'title',
            'penyelenggara' => 'title',
            'deskripsi' => 'sentence',
        ]);
        $data['nama_siswa'] = Siswa::findOrFail($data['siswa_id'])->nama;

        // Upload gambar piala baru dan hapus gambar lama
        if ($request->hasFile('gambar')) {
            if ($prestasi->gambar && Storage::disk('public')->exists($prestasi->gambar)) {
                Storage::disk('public')->delete($prestasi->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('prestasi', 'public');
        }

        $prestasi->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Update',
            'module' => 'Prestasi',
            'description' => 'Memperbarui prestasi: '.$data['judul'],
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil diupdate');
    }

    /**
     * Menghapus data prestasi siswa beserta berkas gambarnya dari database.
     * 
     * @param  \App\Models\Prestasi  $prestasi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Prestasi $prestasi)
    {
        if ($prestasi->gambar && Storage::disk('public')->exists($prestasi->gambar)) {
            Storage::disk('public')->delete($prestasi->gambar);
        }

        $judul = $prestasi->judul;
        $prestasi->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Hapus',
            'module' => 'Prestasi',
            'description' => 'Menghapus prestasi: '.$judul,
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil dihapus');
    }
}
