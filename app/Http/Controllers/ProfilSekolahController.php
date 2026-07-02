<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function editByType($type)
    {
        $judulDefault = ucfirst(str_replace('_', ' ', $type));
        $profil = ProfilSekolah::firstOrCreate(['type' => $type], [
            'judul' => $judulDefault,
            'konten' => '',
            'gambar' => null
        ]);
        
        $judul = $profil->judul ?: $judulDefault;
        return view('admin.profil-sekolah.edit', compact('profil', 'type', 'judul'));
    }

    public function updateByType(Request $request, $type)
    {
        $rules = [
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        if ($type !== 'akreditasi') {
            $rules['judul'] = 'required|string|max:255';
            $rules['konten'] = 'required';
        } else {
            $rules['judul'] = 'nullable|string|max:255';
            $rules['konten'] = 'nullable';
        }

        $request->validate($rules);

        $profil = ProfilSekolah::where('type', $type)->firstOrFail();
        
        $data = [];
        if ($type !== 'akreditasi') {
            $data['judul'] = $request->judul;
            $data['konten'] = $request->konten;
        } else {
            $data['judul'] = $request->judul ?? $profil->judul ?: 'Sertifikat Akreditasi';
            $data['konten'] = $request->konten ?? $profil->konten ?: '';
        }

        if ($request->hasFile('gambar')) {
            if ($profil->gambar && Storage::disk('public')->exists($profil->gambar)) {
                Storage::disk('public')->delete($profil->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('profil', 'public');
        }

        $profil->update($data);

        return redirect()->back()->with('success', 'Profil Sekolah berhasil diperbarui');
    }
}
