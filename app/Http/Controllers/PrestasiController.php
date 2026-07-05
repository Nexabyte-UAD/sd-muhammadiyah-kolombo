<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PrestasiController extends Controller
{
    public function index()
    {
        $prestasis = Prestasi::orderBy('tanggal', 'desc')->paginate(10);
        return view('admin.prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        $kategoriPrestasi = Prestasi::KATEGORI;

        return view('admin.prestasi.create', compact('kategoriPrestasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => ['required', Rule::in(array_keys(Prestasi::KATEGORI))],
            'nama_siswa' => 'required|string|max:255',
            'prestasi_medali' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'judul',
            'kategori',
            'nama_siswa',
            'prestasi_medali',
            'penyelenggara',
            'deskripsi',
            'tanggal',
        ]);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('prestasi', 'public');
        }

        Prestasi::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Tambah',
            'module' => 'Prestasi',
            'description' => 'Menambahkan prestasi: ' . $data['judul'],
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil ditambahkan');
    }

    public function edit(Prestasi $prestasi)
    {
        $kategoriPrestasi = Prestasi::KATEGORI;

        return view('admin.prestasi.edit', compact('prestasi', 'kategoriPrestasi'));
    }

    public function update(Request $request, Prestasi $prestasi)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => ['required', Rule::in(array_keys(Prestasi::KATEGORI))],
            'nama_siswa' => 'required|string|max:255',
            'prestasi_medali' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only([
            'judul',
            'kategori',
            'nama_siswa',
            'prestasi_medali',
            'penyelenggara',
            'deskripsi',
            'tanggal',
        ]);

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
            'description' => 'Memperbarui prestasi: ' . $data['judul'],
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil diupdate');
    }

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
            'description' => 'Menghapus prestasi: ' . $judul,
        ]);

        return redirect()->route('admin.prestasi.index')->with('success', 'Data Prestasi berhasil dihapus');
    }
}
