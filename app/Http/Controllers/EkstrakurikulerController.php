<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ekstrakurikuler;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller EkstrakurikulerController
 * 
 * Mengelola kegiatan ekstrakurikuler SD Muhammadiyah Komplek Kolombo,
 * termasuk pencarian kegiatan, jadwal latihan, pembina, upload dokumentasi foto, dan log audit.
 */
class EkstrakurikulerController extends Controller
{
    /**
     * Menampilkan daftar kegiatan ekstrakurikuler di panel admin.
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

        $query = Ekstrakurikuler::query();

        // Cari berdasarkan nama, deskripsi, pembina, atau jadwal
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('pembina', 'like', "%{$search}%")
                  ->orWhere('jadwal', 'like', "%{$search}%");
            });
        }

        $ekstrakurikulers = $query->paginate($perPage)->withQueryString();

        return view('admin.ekstrakurikuler.index', compact('ekstrakurikulers', 'perPage', 'search'));
    }

    /**
     * Menampilkan formulir tambah kegiatan ekstrakurikuler baru.
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.ekstrakurikuler.create');
    }

    /**
     * Menyimpan data ekstrakurikuler baru ke database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'pembina' => 'nullable|string|max:255',
            'jadwal' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'pembina', 'jadwal']);
        // Format teks input ekstrakurikuler
        $data = $formatter->fields($data, [
            'nama' => 'title',
            'deskripsi' => 'sentence',
            'pembina' => 'name',
            'jadwal' => 'sentence',
        ]);

        // Upload foto kegiatan
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('ekstrakurikuler', 'public');
        }

        Ekstrakurikuler::create($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Tambah',
            'module' => 'Ekstrakurikuler',
            'description' => 'Menambahkan ekstrakurikuler: '.$data['nama'],
        ]);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Data Ekstrakurikuler berhasil ditambahkan');
    }

    /**
     * Menampilkan formulir edit data ekstrakurikuler.
     * 
     * @param  \App\Models\Ekstrakurikuler  $ekstrakurikuler
     * @return \Illuminate\View\View
     */
    public function edit(Ekstrakurikuler $ekstrakurikuler)
    {
        return view('admin.ekstrakurikuler.edit', compact('ekstrakurikuler'));
    }

    /**
     * Memperbarui data ekstrakurikuler di database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ekstrakurikuler  $ekstrakurikuler
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'pembina' => 'nullable|string|max:255',
            'jadwal' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'pembina', 'jadwal']);
        $data = $formatter->fields($data, [
            'nama' => 'title',
            'deskripsi' => 'sentence',
            'pembina' => 'name',
            'jadwal' => 'sentence',
        ]);

        // Upload foto baru dan hapus foto lama
        if ($request->hasFile('foto')) {
            if ($ekstrakurikuler->foto && Storage::disk('public')->exists($ekstrakurikuler->foto)) {
                Storage::disk('public')->delete($ekstrakurikuler->foto);
            }
            $data['foto'] = $request->file('foto')->store('ekstrakurikuler', 'public');
        }

        $ekstrakurikuler->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Update',
            'module' => 'Ekstrakurikuler',
            'description' => 'Memperbarui ekstrakurikuler: '.$data['nama'],
        ]);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Data Ekstrakurikuler berhasil diupdate');
    }

    /**
     * Menghapus data ekstrakurikuler beserta foto dokumentasinya.
     * 
     * @param  \App\Models\Ekstrakurikuler  $ekstrakurikuler
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        if ($ekstrakurikuler->foto && Storage::disk('public')->exists($ekstrakurikuler->foto)) {
            Storage::disk('public')->delete($ekstrakurikuler->foto);
        }

        $nama = $ekstrakurikuler->nama;
        $ekstrakurikuler->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Hapus',
            'module' => 'Ekstrakurikuler',
            'description' => 'Menghapus ekstrakurikuler: '.$nama,
        ]);

        return redirect()->route('admin.ekstrakurikuler.index')->with('success', 'Data Ekstrakurikuler berhasil dihapus');
    }
}
