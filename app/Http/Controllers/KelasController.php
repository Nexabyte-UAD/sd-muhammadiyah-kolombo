<?php

namespace App\Http\Controllers;

use App\Models\GuruStaff;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function edit()
    {
        $kelas = Kelas::orderBy('tingkat')->get()->keyBy('tingkat');
        $gurus = GuruStaff::where('tipe', 'guru')->orderBy('nama')->get();

        return view('admin.kelas.edit', compact('kelas', 'gurus'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wali_kelas' => ['required', 'array'],
            'wali_kelas.*' => [
                'nullable',
                Rule::exists('guru_staffs', 'id')->where(fn ($query) => $query->where('tipe', 'guru')),
            ],
        ]);

        DB::transaction(function () use ($request) {
            foreach (range(1, 6) as $tingkat) {
                Kelas::updateOrCreate(
                    ['tingkat' => (string) $tingkat],
                    ['wali_kelas_id' => $request->input("wali_kelas.{$tingkat}")]
                );
            }
        });

        return redirect()->route('admin.kelas.edit')
            ->with('success', 'Wali kelas berhasil diperbarui.');
    }
}
