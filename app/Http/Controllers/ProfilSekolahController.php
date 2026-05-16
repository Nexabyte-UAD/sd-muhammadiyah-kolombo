<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilSekolahController extends Controller
{
    public function editByType($type)
    {
        $profil = ProfilSekolah::firstOrCreate(['type' => $type], [
            'judul' => '',
            'konten' => '',
            'gambar' => null
        ]);
        
        $judul = ucfirst(str_replace('_', ' ', $type));
        return view('admin.profil-sekolah.edit', compact('profil', 'type', 'judul'));
    }

    public function updateByType(Request $request, $type)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $profil = ProfilSekolah::where('type', $type)->firstOrFail();
        $data = [
            'judul' => $request->judul,
            'konten' => $request->konten
        ];

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
