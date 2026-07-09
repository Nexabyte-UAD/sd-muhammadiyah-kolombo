<?php

namespace App\Http\Controllers;

use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = $request->query('search');

        $query = Kelas::with('waliKelas')
            ->withCount(['siswas' => fn ($query) => $query->where('status', 'aktif')]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tingkat', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%")
                  ->orWhereHas('waliKelas', function ($wq) use ($search) {
                      $wq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $kelas = $query->orderByRaw('urutan IS NULL')
            ->orderBy('urutan')
            ->orderBy('tingkat')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.kelas.index', compact('kelas', 'perPage', 'search'));
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
        $data = $this->validated($request, $kelas);
        if (($data['kapasitas'] ?? null) && $kelas->siswas()->where('status', 'aktif')->count() > $data['kapasitas']) {
            throw ValidationException::withMessages([
                'kapasitas' => 'Kapasitas tidak boleh lebih kecil dari jumlah siswa aktif saat ini.',
            ]);
        }
        $kelas->update($data);
        Siswa::withTrashed()->where('kelas_id', $kelas->id)->update(['kelas' => $kelas->tingkat]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        if (Siswa::withTrashed()->where('kelas_id', $kelas->id)->exists()) {
            throw ValidationException::withMessages([
                'kelas' => 'Kelas tidak dapat dihapus karena masih memiliki siswa.',
            ]);
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    private function validated(Request $request, ?Kelas $kelas = null): array
    {
        $request->merge([
            'tingkat' => Kelas::normalizeLabel($request->input('tingkat'), true),
            'jurusan' => Kelas::normalizeLabel($request->input('jurusan')),
        ]);

        $data = $request->validate([
            'tingkat' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'tingkat')->ignore($kelas?->id),
            ],
            'jurusan' => ['nullable', 'string', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:999'],
            'tahun_ajaran' => ['nullable', 'regex:/^\d{4}\/\d{4}$/'],
            'kapasitas' => ['nullable', 'integer', 'min:1', 'max:999'],
            'wali_kelas_id' => [
                'nullable',
                Rule::exists('guru_staffs', 'id')->where(
                    fn ($query) => $query->where('tipe', 'guru')
                ),
                Rule::unique('kelas', 'wali_kelas_id')
                    ->where(fn ($query) => $query->where('tahun_ajaran', $request->input('tahun_ajaran')))
                    ->ignore($kelas?->id),
            ],
        ]);

        if (! empty($data['tahun_ajaran'])) {
            [$awal, $akhir] = array_map('intval', explode('/', $data['tahun_ajaran']));
            if ($akhir !== $awal + 1) {
                throw ValidationException::withMessages([
                    'tahun_ajaran' => 'Tahun ajaran harus berurutan, contoh: 2026/2027.',
                ]);
            }
        }

        return $data;
    }

    private function gurus()
    {
        return GuruStaff::where('tipe', 'guru')->orderBy('nama')->get();
    }
}
