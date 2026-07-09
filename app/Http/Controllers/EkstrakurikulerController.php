<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Ekstrakurikuler;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EkstrakurikulerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = $request->query('search');

        $query = Ekstrakurikuler::query();

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

    public function create()
    {
        return view('admin.ekstrakurikuler.create');
    }

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
        $data = $formatter->fields($data, [
            'nama' => 'title',
            'deskripsi' => 'sentence',
            'pembina' => 'name',
            'jadwal' => 'sentence',
        ]);

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

    public function edit(Ekstrakurikuler $ekstrakurikuler)
    {
        return view('admin.ekstrakurikuler.edit', compact('ekstrakurikuler'));
    }

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
