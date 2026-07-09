<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Prestasi;
use App\Models\Siswa;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $kategori = (string) $request->query('kategori', '');

        $query = Prestasi::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('nama_siswa', 'like', "%{$search}%")
                    ->orWhere('prestasi_medali', 'like', "%{$search}%")
                    ->orWhere('penyelenggara', 'like', "%{$search}%");
            });
        }

        if (array_key_exists($kategori, Prestasi::KATEGORI)) {
            $query->where('kategori', $kategori);
        }

        $prestasis = $query->orderBy('tanggal', 'desc')->paginate(12)->withQueryString();
        $kategoriPrestasi = Prestasi::KATEGORI;

        return view('admin.prestasi.index', compact('prestasis', 'kategoriPrestasi', 'search', 'kategori'));
    }

    public function create()
    {
        $kategoriPrestasi = Prestasi::KATEGORI;
        $siswas = Siswa::orderBy('nama')->get();

        return view('admin.prestasi.create', compact('kategoriPrestasi', 'siswas'));
    }

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
        $data = $formatter->fields($data, [
            'judul' => 'title',
            'prestasi_medali' => 'title',
            'penyelenggara' => 'title',
            'deskripsi' => 'sentence',
        ]);
        $data['nama_siswa'] = Siswa::findOrFail($data['siswa_id'])->nama;

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

    public function edit(Prestasi $prestasi)
    {
        $kategoriPrestasi = Prestasi::KATEGORI;
        $siswas = Siswa::orderBy('nama')->get();

        return view('admin.prestasi.edit', compact('prestasi', 'kategoriPrestasi', 'siswas'));
    }

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
