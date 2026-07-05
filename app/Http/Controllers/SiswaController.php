<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\ActivityLog;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        if ($kelas && Kelas::where('tingkat', $kelas)->exists()) {
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

        $daftarKelas = $this->daftarKelas();

        return view('admin.siswa.index', compact('siswas', 'status', 'kelas', 'search', 'daftarKelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.siswa.create', [
            'daftarKelas' => $this->daftarKelas(),
        ]);
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
            'kelas' => [
                'nullable',
                'required_if:status,aktif',
                Rule::exists('kelas', 'tingkat'),
            ],
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
            'user_id' => auth()->id(),
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
        return view('admin.siswa.edit', [
            'siswa' => $siswa,
            'daftarKelas' => $this->daftarKelas(),
        ]);
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
            'kelas' => [
                'nullable',
                'required_if:status,aktif',
                Rule::exists('kelas', 'tingkat'),
            ],
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
            'user_id' => auth()->id(),
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
            'user_id' => auth()->id(),
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
        $daftarKelas = $this->daftarKelas();
        $rekapSiswa = [];
        foreach ($daftarKelas as $kelas) {
            $rekapSiswa[$kelas->tingkat] = Siswa::aktif()->kelas($kelas->tingkat)->count();
        }
        $rekapSiswa['alumni'] = Siswa::alumni()->count();

        return view('admin.siswa.promote', compact('rekapSiswa', 'daftarKelas'));
    }

    /**
     * Process bulk promotion and graduation.
     */
    public function promote(Request $request)
    {
        DB::transaction(function () {
            $currentYear = date('Y');
            $daftarKelas = $this->daftarKelas()->pluck('tingkat')->values();

            if ($daftarKelas->isEmpty()) {
                return;
            }

            Siswa::aktif()->kelas($daftarKelas->last())->update([
                    'status' => 'alumni',
                    'kelas' => null,
                    'tahun_lulus' => $currentYear
                ]);

            for ($index = $daftarKelas->count() - 2; $index >= 0; $index--) {
                Siswa::aktif()
                    ->kelas($daftarKelas[$index])
                    ->update([
                        'kelas' => $daftarKelas[$index + 1]
                    ]);
            }
        });

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action_type' => 'Update',
            'module' => 'Siswa',
            'description' => 'Melakukan Kenaikan Kelas & Kelulusan Massal Siswa',
        ]);

        return redirect()->route('admin.siswa.promote.page')->with('success', 'Kenaikan kelas massal dan kelulusan berhasil diproses!');
    }

    private function daftarKelas()
    {
        return Kelas::orderBy('tingkat')->get();
    }
}
