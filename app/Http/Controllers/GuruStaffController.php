<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GuruStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuruStaffController extends Controller
{
    public function index()
    {
        $tipe = request()->query('tipe', 'guru');
        if (! in_array($tipe, ['guru', 'staf'], true)) {
            $tipe = 'guru';
        }
        $gurus = GuruStaff::where('tipe', $tipe)->orderBy('nama', 'asc')->paginate(10);

        return view('admin.guru-staff.index', compact('gurus', 'tipe'));
    }

    public function create()
    {
        $tipe = request()->query('tipe', 'guru');
        if (! in_array($tipe, ['guru', 'staf'], true)) {
            $tipe = 'guru';
        }

        return view('admin.guru-staff.create', [
            'tipe' => $tipe,
            'jenisKelamin' => GuruStaff::JENIS_KELAMIN,
            'statusKepegawaian' => GuruStaff::STATUS_KEPEGAWAIAN,
            'pendidikanTerakhir' => GuruStaff::PENDIDIKAN_TERAKHIR,
            'daftarAgama' => GuruStaff::AGAMA,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', Rule::in(array_keys(GuruStaff::JENIS_KELAMIN))],
            'jabatan' => 'required|string|max:255',
            'bidang_tugas' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:50',
            'status_kepegawaian' => ['required', Rule::in(array_keys(GuruStaff::STATUS_KEPEGAWAIAN))],
            'pendidikan_terakhir' => ['required', Rule::in(array_keys(GuruStaff::PENDIDIKAN_TERAKHIR))],
            'agama' => ['required', Rule::in(array_keys(GuruStaff::AGAMA))],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'tipe',
            'nama',
            'jenis_kelamin',
            'jabatan',
            'bidang_tugas',
            'nip',
            'status_kepegawaian',
            'pendidikan_terakhir',
            'agama',
        ]);

        $fotoBaru = $request->hasFile('foto')
            ? $request->file('foto')->store('guru-staff', 'public')
            : null;

        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        try {
            DB::transaction(function () use ($data) {
                GuruStaff::create($data);

                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'Tambah',
                    'module' => 'Struktural',
                    'description' => 'Menambahkan '.$data['tipe'].': '.$data['nama'],
                ]);
            });
        } catch (\Throwable $exception) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $exception;
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil ditambahkan');
    }

    public function edit(GuruStaff $guruStaff)
    {
        return view('admin.guru-staff.edit', [
            'guru' => $guruStaff,
            'jenisKelamin' => GuruStaff::JENIS_KELAMIN,
            'statusKepegawaian' => GuruStaff::STATUS_KEPEGAWAIAN,
            'pendidikanTerakhir' => GuruStaff::PENDIDIKAN_TERAKHIR,
            'daftarAgama' => GuruStaff::AGAMA,
        ]);
    }

    public function update(Request $request, GuruStaff $guruStaff)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', Rule::in(array_keys(GuruStaff::JENIS_KELAMIN))],
            'jabatan' => 'required|string|max:255',
            'bidang_tugas' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:50',
            'status_kepegawaian' => ['required', Rule::in(array_keys(GuruStaff::STATUS_KEPEGAWAIAN))],
            'pendidikan_terakhir' => ['required', Rule::in(array_keys(GuruStaff::PENDIDIKAN_TERAKHIR))],
            'agama' => ['required', Rule::in(array_keys(GuruStaff::AGAMA))],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'tipe',
            'nama',
            'jenis_kelamin',
            'jabatan',
            'bidang_tugas',
            'nip',
            'status_kepegawaian',
            'pendidikan_terakhir',
            'agama',
        ]);

        $fotoLama = $guruStaff->foto;
        $fotoBaru = $request->hasFile('foto')
            ? $request->file('foto')->store('guru-staff', 'public')
            : null;

        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        try {
            DB::transaction(function () use ($guruStaff, $data) {
                $guruStaff->update($data);

                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action_type' => 'Update',
                    'module' => 'Struktural',
                    'description' => 'Memperbarui '.$data['tipe'].': '.$data['nama'],
                ]);
            });
        } catch (\Throwable $exception) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $exception;
        }

        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil diupdate');
    }

    public function destroy(GuruStaff $guruStaff)
    {
        $nama = $guruStaff->nama;
        $tipe = $guruStaff->tipe;
        $foto = $guruStaff->foto;

        DB::transaction(function () use ($guruStaff, $nama, $tipe) {
            $guruStaff->delete();

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action_type' => 'Hapus',
                'module' => 'Struktural',
                'description' => 'Menghapus '.$tipe.': '.$nama,
            ]);
        });

        if ($foto) {
            Storage::disk('public')->delete($foto);
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $tipe])->with('success', 'Data berhasil dihapus');
    }
}
