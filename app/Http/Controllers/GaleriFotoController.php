<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GaleriFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriFotoController extends Controller
{
    /**
     * Menampilkan daftar Galeri Foto di Admin Panel.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $kategori = $request->query('kategori');
        $search = $request->query('search');

        $query = GaleriFoto::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $fotos = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $kategoriList = GaleriFoto::KATEGORI;

        return view('admin.galeri_foto.index', compact('fotos', 'kategoriList', 'kategori', 'search', 'perPage'));
    }

    /**
     * Menampilkan form tambah foto.
     */
    public function create()
    {
        $kategoriList = GaleriFoto::KATEGORI;
        return view('admin.galeri_foto.create', compact('kategoriList'));
    }

    /**
     * Menyimpan foto baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('galeri_fotos', 'public');
        }

        $foto = GaleriFoto::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Foto',
            'action_type' => 'Tambah',
            'description' => "Menambahkan foto galeri baru '{$foto->judul}'.",
        ]);

        return redirect()->route('admin.galeri-foto.index')->with('success', 'Foto galeri berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit foto.
     */
    public function edit(GaleriFoto $galeriFoto)
    {
        $kategoriList = GaleriFoto::KATEGORI;
        return view('admin.galeri_foto.edit', compact('galeriFoto', 'kategoriList'));
    }

    /**
     * Memperbarui foto di database.
     */
    public function update(Request $request, GaleriFoto $galeriFoto)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            if ($galeriFoto->foto && Storage::disk('public')->exists($galeriFoto->foto)) {
                Storage::disk('public')->delete($galeriFoto->foto);
            }
            $validated['foto'] = $request->file('foto')->store('galeri_fotos', 'public');
        }

        $galeriFoto->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Foto',
            'action_type' => 'Update',
            'description' => "Memperbarui foto galeri '{$galeriFoto->judul}'.",
        ]);

        return redirect()->route('admin.galeri-foto.index')->with('success', 'Foto galeri berhasil diperbarui!');
    }

    /**
     * Menghapus foto dari database.
     */
    public function destroy(GaleriFoto $galeriFoto)
    {
        $judul = $galeriFoto->judul;

        if ($galeriFoto->foto && Storage::disk('public')->exists($galeriFoto->foto)) {
            Storage::disk('public')->delete($galeriFoto->foto);
        }

        $galeriFoto->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Galeri Foto',
            'action_type' => 'Hapus',
            'description' => "Menghapus foto galeri '{$judul}'.",
        ]);

        return redirect()->route('admin.galeri-foto.index')->with('success', 'Foto galeri berhasil dihapus!');
    }
}
