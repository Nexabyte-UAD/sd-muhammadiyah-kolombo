<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\GuruStaff;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Controller GuruStaffController
 * 
 * Mengelola data kepegawaian Guru (Pendidik) dan Staf (Tenaga Kependidikan)
 * di SD Muhammadiyah Komplek Kolombo, termasuk upload foto dan log audit.
 */
class GuruStaffController extends Controller
{
    /**
     * Menampilkan daftar Guru atau Staf.
     * Mendukung pencarian berdasarkan Nama, NIP, Jabatan, dan Bidang Tugas.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Tentukan tipe apakah guru atau staf
        $tipe = $request->query('tipe', 'guru');
        if (! in_array($tipe, ['guru', 'staf'], true)) {
            $tipe = 'guru';
        }
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = $request->query('search');

        // Query berdasarkan filter tipe pegawai
        $query = GuruStaff::where('tipe', $tipe);

        // Filter pencarian teks
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('bidang_tugas', 'like', "%{$search}%");
            });
        }

        $gurus = $query->orderBy('nama', 'asc')->paginate($perPage)->withQueryString();

        return view('admin.guru-staff.index', compact('gurus', 'tipe', 'perPage', 'search'));
    }

    /**
     * Menampilkan halaman formulir tambah Guru/Staf baru.
     * 
     * @return \Illuminate\View\View
     */
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

    /**
     * Menyimpan data Guru/Staf baru ke database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', Rule::in(array_keys(GuruStaff::JENIS_KELAMIN))],
            'jabatan' => 'required|string|max:255',
            'bidang_tugas' => 'nullable|string|max:255',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('guru_staffs', 'nip')],
            'status_kepegawaian' => ['nullable', Rule::in(array_keys(GuruStaff::STATUS_KEPEGAWAIAN))],
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
        
        // Merapikan format nama dan jabatan guru
        $data = $formatter->fields($data, [
            'nama' => 'name',
            'jabatan' => 'title',
            'bidang_tugas' => 'title',
        ]);
        $data['status_kepegawaian'] = $request->filled('status_kepegawaian')
            ? $request->status_kepegawaian
            : null;

        // Proses upload foto profil guru/staf
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
            // Hapus file yang terlanjur terupload jika insert gagal
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $exception;
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil ditambahkan');
    }

    /**
     * Menampilkan halaman edit Guru/Staf.
     * 
     * @param  \App\Models\GuruStaff  $guruStaff
     * @return \Illuminate\View\View
     */
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

    /**
     * Memperbarui data Guru/Staf di database.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GuruStaff  $guruStaff
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, GuruStaff $guruStaff, IndonesianTextFormatter $formatter)
    {
        $request->validate([
            'tipe' => 'required|in:guru,staf',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', Rule::in(array_keys(GuruStaff::JENIS_KELAMIN))],
            'jabatan' => 'required|string|max:255',
            'bidang_tugas' => 'nullable|string|max:255',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('guru_staffs', 'nip')->ignore($guruStaff->id),
            ],
            'status_kepegawaian' => ['nullable', Rule::in(array_keys(GuruStaff::STATUS_KEPEGAWAIAN))],
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
        $data = $formatter->fields($data, [
            'nama' => 'name',
            'jabatan' => 'title',
            'bidang_tugas' => 'title',
        ]);
        $data['status_kepegawaian'] = $request->filled('status_kepegawaian')
            ? $request->status_kepegawaian
            : null;

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

        // Hapus foto lama jika upload foto baru sukses
        if ($fotoBaru && $fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $data['tipe']])->with('success', 'Data berhasil diupdate');
    }

    /**
     * Menghapus data Guru/Staf dari database.
     * 
     * @param  \App\Models\GuruStaff  $guruStaff
     * @return \Illuminate\Http\RedirectResponse
     */
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

        // Hapus berkas foto dari penyimpanan
        if ($foto) {
            Storage::disk('public')->delete($foto);
        }

        return redirect()->route('admin.guru-staff.index', ['tipe' => $tipe])->with('success', 'Data berhasil dihapus');
    }
}
