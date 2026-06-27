<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'aktif');
        $kelas = $request->query('kelas');
        $search = $request->query('search');

        $query = Siswa::query();

        if ($status === 'alumni') {
            $query->alumni();
        } else {
            $query->aktif();
        }

        if ($kelas && in_array($kelas, ['1', '2', '3', '4', '5', '6'])) {
            $query->kelas($kelas);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswas = $query->orderBy('nama', 'asc')->paginate(10)->withQueryString();

        return view('admin.siswa.index', compact('siswas', 'status', 'kelas', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50|unique:siswas,nisn',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'kelas' => 'nullable|in:1,2,3,4,5,6',
            'status' => 'required|in:aktif,alumni',
            'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'tahun_lulus' => 'nullable|required_if:status,alumni|integer|min:2000|max:' . (date('Y') + 5),
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('foto');

        // Clean up kelas if alumni
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        Siswa::create($data);

        ActivityLog::create([
            'action_type' => 'Tambah',
            'module' => 'Siswa',
            'description' => 'Menambahkan siswa baru: ' . $data['nama'] . ($data['status'] === 'alumni' ? ' (Alumni)' : ' (Kelas ' . $data['kelas'] . ')'),
        ]);

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50|unique:siswas,nisn,' . $siswa->id,
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'kelas' => 'nullable|in:1,2,3,4,5,6',
            'status' => 'required|in:aktif,alumni',
            'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'tahun_lulus' => 'nullable|required_if:status,alumni|integer|min:2000|max:' . (date('Y') + 5),
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('foto');

        // Clean up kelas if alumni
        if ($data['status'] === 'alumni') {
            $data['kelas'] = null;
        } else {
            $data['tahun_lulus'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa', 'public');
        }

        $siswa->update($data);

        ActivityLog::create([
            'action_type' => 'Update',
            'module' => 'Siswa',
            'description' => 'Memperbarui biodata siswa: ' . $data['nama'],
        ]);

        return redirect()->route('admin.siswa.index', ['status' => $data['status']])->with('success', 'Data siswa berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        $nama = $siswa->nama;
        $status = $siswa->status;
        $siswa->delete();

        ActivityLog::create([
            'action_type' => 'Hapus',
            'module' => 'Siswa',
            'description' => 'Menghapus data siswa: ' . $nama,
        ]);

        return redirect()->route('admin.siswa.index', ['status' => $status])->with('success', 'Data siswa berhasil dihapus');
    }

    /**
     * Show the promotion dashboard page.
     */
    public function promotePage()
    {
        $rekapSiswa = [];
        for ($i = 1; $i <= 6; $i++) {
            $rekapSiswa[$i] = Siswa::aktif()->kelas($i)->count();
        }
        $rekapSiswa['alumni'] = Siswa::alumni()->count();

        return view('admin.siswa.promote', compact('rekapSiswa'));
    }

    /**
     * Process bulk promotion and graduation.
     */
    public function promote(Request $request)
    {
        DB::transaction(function () {
            $currentYear = date('Y');

            // 1. Graduate Class 6 to Alumni
            Siswa::aktif()
                ->kelas('6')
                ->update([
                    'status' => 'alumni',
                    'kelas' => null,
                    'tahun_lulus' => $currentYear
                ]);

            // 2. Promote classes in descending order (5->6, 4->5, 3->4, 2->3, 1->2)
            for ($kelas = 5; $kelas >= 1; $kelas--) {
                Siswa::aktif()
                    ->kelas((string)$kelas)
                    ->update([
                        'kelas' => (string)($kelas + 1)
                    ]);
            }
        });

        ActivityLog::create([
            'action_type' => 'Update',
            'module' => 'Siswa',
            'description' => 'Melakukan Kenaikan Kelas & Kelulusan Massal Siswa',
        ]);

        return redirect()->route('admin.siswa.promote.page')->with('success', 'Kenaikan kelas massal dan kelulusan berhasil diproses!');
    }
}
