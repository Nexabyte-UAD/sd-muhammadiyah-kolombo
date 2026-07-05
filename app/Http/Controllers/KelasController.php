<?php

namespace App\Http\Controllers;

use App\Models\GuruStaff;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('waliKelas')->orderBy('tingkat')->paginate(10);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create', [
            'gurus' => $this->gurus(),
        ]);
    }

    public function store(Request $request)
    {
        Kelas::create($this->validated($request));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', [
            'kelas' => $kelas,
            'gurus' => $this->gurus(),
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $kelas->update($this->validated($request, $kelas));

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    private function validated(Request $request, ?Kelas $kelas = null): array
    {
        return $request->validate([
            'tingkat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'tingkat')->ignore($kelas?->id),
            ],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'wali_kelas_id' => [
                'nullable',
                Rule::exists('guru_staffs', 'id')->where(
                    fn ($query) => $query->where('tipe', 'guru')
                ),
            ],
        ]);
    }

    private function gurus()
    {
        return GuruStaff::where('tipe', 'guru')->orderBy('nama')->get();
    }
}
