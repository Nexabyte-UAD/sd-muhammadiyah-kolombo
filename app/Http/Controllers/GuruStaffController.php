<?php

namespace App\Http\Controllers;

use App\Models\GuruStaff;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuruStaffController extends Controller
{
    public function index()
    {
        $tipe = request()->query('tipe', 'guru');
        $gurus = GuruStaff::where('tipe', $tipe)->orderBy('nama', 'asc')->paginate(10);
        return view('admin.guru-staff.index', compact('gurus', 'tipe'));
    }

    public function create()
    {
        $tipe = request()->query('tipe', 'guru');
        return view('admin.guru-staff.create', compact('tipe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['tipe', 'nama', 'jabatan', 'nip']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('guru-staff', 'public');
        }

        GuruStaff::create($data);

        ActivityLog::create([
            'action_type' => 'Tambah',
            'module' => 'Struktural',
            'description' => 'Menambahkan ' . $data['tipe'] . ': ' . $data['nama'],
        ]);

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(GuruStaff $guru)
    {
        return view('admin.guru-staff.edit', compact('guru'));
    }

    public function update(Request $request, GuruStaff $guru)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->only(['tipe', 'nama', 'jabatan', 'nip']);

        if ($request->hasFile('foto')) {
            if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
                Storage::disk('public')->delete($guru->foto);
            }
            $data['foto'] = $request->file('foto')->store('guru-staff', 'public');
        }

        $guru->update($data);

        ActivityLog::create([
            'action_type' => 'Update',
            'module' => 'Struktural',
            'description' => 'Memperbarui ' . $data['tipe'] . ': ' . $data['nama'],
        ]);

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil diupdate');
    }

    public function destroy(GuruStaff $guru)
    {
        if ($guru->foto && Storage::disk('public')->exists($guru->foto)) {
            Storage::disk('public')->delete($guru->foto);
        }
        
        $nama = $guru->nama;
        $tipe = $guru->tipe;
        $guru->delete();

        ActivityLog::create([
            'action_type' => 'Hapus',
            'module' => 'Struktural',
            'description' => 'Menghapus ' . $tipe . ': ' . $nama,
        ]);

        return redirect()->route('admin.guru-staff.index', ['tipe' => $tipe])->with('success', 'Data berhasil dihapus');
    }
}
