<?php

namespace App\Http\Controllers;

use App\Models\ProfilSekolah;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller ProfilSekolahController
 * 
 * Mengelola pembaruan halaman profil statis sekolah (Tentang Sekolah, Visi & Misi,
 * Sambutan Kepala Sekolah, dan Akreditasi), termasuk editor HTML dan gambar bukti akreditasi.
 */
class ProfilSekolahController extends Controller
{
    /**
     * Menampilkan halaman formulir edit berdasarkan tipe profil sekolah.
     * Menggunakan firstOrCreate untuk membuat data awal secara otomatis jika belum ada di database.
     * 
     * @param  string  $type  Jenis tipe halaman (tentang, sambutan, visi_misi, akreditasi)
     * @return \Illuminate\View\View
     */
    public function editByType($type)
    {
        // Tolak request jika jenis tipe tidak dikenal
        abort_unless(in_array($type, ProfilSekolah::TYPES, true), 404);

        $judulDefault = ucfirst(str_replace('_', ' ', $type));
        $profil = ProfilSekolah::firstOrCreate(['type' => $type], [
            'judul' => $judulDefault,
            'konten' => '',
            'gambar' => null,
        ]);

        $judul = $profil->judul ?: $judulDefault;

        return view('admin.profil-sekolah.edit', compact('profil', 'type', 'judul'));
    }

    /**
     * Memperbarui konten halaman profil sekolah berdasarkan tipenya.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $type  Jenis tipe halaman
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateByType(Request $request, $type, IndonesianTextFormatter $formatter)
    {
        abort_unless(in_array($type, ProfilSekolah::TYPES, true), 404);

        // Aturan validasi dinamis (khusus tipe akreditasi, judul dan konten opsional karena fokus ke unggah gambar sertifikat)
        $rules = [
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        
        // Memformat konten teks dan tag HTML
        $data = $formatter->fields($data, ['judul' => 'title', 'konten' => 'html']);

        // Upload berkas gambar pendukung baru (serta menghapus berkas lama)
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
