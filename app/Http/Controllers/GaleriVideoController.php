<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GaleriVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriVideoController extends Controller
{
    /**
     * Menampilkan daftar Galeri Video di Admin Panel.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $kategori = $request->query('kategori');
        $search = $request->query('search');

        $query = GaleriVideo::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $videos = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $kategoriList = GaleriVideo::KATEGORI;

        return view('admin.galeri_video.index', compact('videos', 'kategoriList', 'kategori', 'search', 'perPage'));
    }

    /**
     * Menampilkan form tambah video.
     */
    public function create()
    {
        $kategoriList = GaleriVideo::KATEGORI;
        return view('admin.galeri_video.create', compact('kategoriList'));
    }

    /**
     * Menyimpan video baru ke database.
     */
    public function store(Request $request)
    {
        if ($request->filled('youtube_url') && !preg_match('/^https?:\/\//i', $request->input('youtube_url'))) {
            $request->merge([
                'youtube_url' => 'https://' . ltrim($request->input('youtube_url')),
            ]);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'youtube_url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('galeri_videos', 'public');
        }

        $video = GaleriVideo::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Video',
            'action_type' => 'Tambah',
            'description' => "Menambahkan video galeri baru '{$video->judul}'.",
        ]);

        return redirect()->route('admin.galeri-video.index')->with('success', 'Video galeri berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit video.
     */
    public function edit(GaleriVideo $galeriVideo)
    {
        $kategoriList = GaleriVideo::KATEGORI;
        return view('admin.galeri_video.edit', compact('galeriVideo', 'kategoriList'));
    }

    /**
     * Memperbarui video di database.
     */
    public function update(Request $request, GaleriVideo $galeriVideo)
    {
        if ($request->filled('youtube_url') && !preg_match('/^https?:\/\//i', $request->input('youtube_url'))) {
            $request->merge([
                'youtube_url' => 'https://' . ltrim($request->input('youtube_url')),
            ]);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'youtube_url' => 'required|url|max:500',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($galeriVideo->thumbnail && Storage::disk('public')->exists($galeriVideo->thumbnail)) {
                Storage::disk('public')->delete($galeriVideo->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('galeri_videos', 'public');
        }

        $galeriVideo->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Video',
            'action_type' => 'Update',
            'description' => "Memperbarui video galeri '{$galeriVideo->judul}'.",
        ]);

        return redirect()->route('admin.galeri-video.index')->with('success', 'Video galeri berhasil diperbarui!');
    }

    /**
     * Menghapus video dari database.
     */
    public function destroy(GaleriVideo $galeriVideo)
    {
        $judul = $galeriVideo->judul;

        if ($galeriVideo->thumbnail && Storage::disk('public')->exists($galeriVideo->thumbnail)) {
            Storage::disk('public')->delete($galeriVideo->thumbnail);
        }

        $galeriVideo->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Video',
            'action_type' => 'Hapus',
            'description' => "Menghapus video galeri '{$judul}'.",
        ]);

        return redirect()->route('admin.galeri-video.index')->with('success', 'Video galeri berhasil dihapus!');
    }
}
