<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GuruMenulis;
use App\Models\GuruStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuruMenulisController extends Controller
{
    /**
     * Menampilkan daftar artikel Guru Menulis di Admin Panel.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $kategori = (string) $request->query('kategori', '');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $query = GuruMenulis::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        $artikels = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $kategoriList = GuruMenulis::KATEGORI;

        return view('admin.guru_menulis.index', compact('artikels', 'kategoriList', 'kategori', 'search', 'perPage'));
    }

    /**
     * Form tambah artikel Guru Menulis.
     */
    public function create()
    {
        $gurus = GuruStaff::orderBy('nama')->get();
        $kategoriList = GuruMenulis::KATEGORI;
        return view('admin.guru_menulis.create', compact('gurus', 'kategoriList'));
    }

    /**
     * Menyimpan artikel baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'guru_staff_id' => 'nullable|exists:guru_staffs,id',
            'kategori' => 'required|string|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'isi' => 'required|string',
            'status' => 'required|in:draft,published',
            'tanggal' => 'nullable|date',
        ]);

        $validated['slug'] = Str::slug($validated['judul']) . '-' . Str::random(5);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('guru_menulis', 'public');
        }

        $artikel = GuruMenulis::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Guru Menulis',
            'action_type' => 'Tambah',
            'description' => "Menambahkan artikel karya guru baru '{$artikel->judul}'.",
        ]);

        return redirect()->route('admin.guru-menulis.index')->with('success', 'Artikel karya guru berhasil ditambahkan!');
    }

    /**
     * Form edit artikel.
     */
    public function edit(GuruMenulis $guruMenuli)
    {
        $gurus = GuruStaff::orderBy('nama')->get();
        $kategoriList = GuruMenulis::KATEGORI;
        return view('admin.guru_menulis.edit', compact('guruMenuli', 'gurus', 'kategoriList'));
    }

    /**
     * Memperbarui artikel.
     */
    public function update(Request $request, GuruMenulis $guruMenuli)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'guru_staff_id' => 'nullable|exists:guru_staffs,id',
            'kategori' => 'required|string|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'isi' => 'required|string',
            'status' => 'required|in:draft,published',
            'tanggal' => 'nullable|date',
        ]);

        if ($request->hasFile('gambar')) {
            if ($guruMenuli->gambar && Storage::disk('public')->exists($guruMenuli->gambar)) {
                Storage::disk('public')->delete($guruMenuli->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('guru_menulis', 'public');
        }

        $guruMenuli->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Guru Menulis',
            'action_type' => 'Update',
            'description' => "Memperbarui artikel karya guru '{$guruMenuli->judul}'.",
        ]);

        return redirect()->route('admin.guru-menulis.index')->with('success', 'Artikel karya guru berhasil diperbarui!');
    }

    /**
     * Menghapus artikel.
     */
    public function destroy(GuruMenulis $guruMenuli)
    {
        $judul = $guruMenuli->judul;

        if ($guruMenuli->gambar && Storage::disk('public')->exists($guruMenuli->gambar)) {
            Storage::disk('public')->delete($guruMenuli->gambar);
        }

        $guruMenuli->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Guru Menulis',
            'action_type' => 'Hapus',
            'description' => "Menghapus artikel karya guru '{$judul}'.",
        ]);

        return redirect()->route('admin.guru-menulis.index')->with('success', 'Artikel karya guru berhasil dihapus!');
    }
}
