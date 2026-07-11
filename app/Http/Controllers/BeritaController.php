<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Berita;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller BeritaController
 * 
 * Mengelola pembuatan dan publikasi berita/pengumuman sekolah,
 * termasuk editor artikel HTML, upload cover gambar, status publikasi, dan log audit.
 */
class BeritaController extends Controller
{
    /**
     * Menampilkan daftar berita sekolah di panel admin.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $query = Berita::query();

        // Cari berdasarkan judul, konten isi berita, atau status publish/draft
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('isi', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $beritas = $query->orderBy('tanggal', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.berita.index', compact('beritas', 'search', 'perPage'));
    }

    /**
     * Menampilkan formulir tambah berita baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.berita.create');
    }

    /**
     * Menyimpan berita baru ke database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:draft,published',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['judul', 'isi', 'tanggal', 'status']);
        // Format judul (Title Case) dan isi HTML berita agar rapi
        $data = $formatter->fields($data, ['judul' => 'title', 'isi' => 'html']);
        $data['user_id'] = auth()->id();

        // Upload gambar sampul berita
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('berita', 'public');
            $data['gambar'] = $path;
        }

        Berita::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Tambah',
            'module' => 'Berita',
            'description' => 'Menambahkan berita baru: '.Str::limit($data['judul'], 50),
        ]);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan');
    }

    /**
     * Menampilkan formulir edit berita.
     * 
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\View\View
     */
    public function edit(Berita $berita)
    {
        return view('admin.berita.edit', compact('berita'));
    }

    /**
     * Memperbarui data berita di database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Berita  $berita
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Berita $berita, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
            'tanggal' => 'required|date',
            'status' => 'required|in:draft,published',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['judul', 'isi', 'tanggal', 'status']);
        $data = $formatter->fields($data, ['judul' => 'title', 'isi' => 'html']);

        // Upload gambar baru dan hapus gambar lama
        if ($request->hasFile('gambar')) {
            if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $path = $request->file('gambar')->store('berita', 'public');
            $data['gambar'] = $path;
        }

        $berita->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Update',
            'module' => 'Berita',
            'description' => 'Memperbarui berita: '.Str::limit($data['judul'], 50),
        ]);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diupdate');
    }

    /**
     * Menghapus berita beserta berkas gambar cover-nya dari penyimpanan.
     * 
     * @param  \App\Models\Berita  $berita
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Berita $berita)
    {
        if ($berita->gambar && Storage::disk('public')->exists($berita->gambar)) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $judul = $berita->judul;
        $berita->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Hapus',
            'module' => 'Berita',
            'description' => 'Menghapus berita: '.Str::limit($judul, 50),
        ]);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus');
    }
}
