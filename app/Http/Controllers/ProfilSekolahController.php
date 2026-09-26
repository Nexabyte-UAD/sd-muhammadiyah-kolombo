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

        $judulDefault = match ($type) {
            'spmb' => 'Informasi SPMB',
            'tentang' => 'Membentuk Generasi Islami & Berprestasi',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
        $profil = ProfilSekolah::firstOrCreate(['type' => $type], [
            'judul' => $judulDefault,
            'konten' => '',
            'gambar' => null,
        ]);

        if ($type === 'tentang' && (empty($profil->judul) || in_array($profil->judul, ['Islami & Berprestasi', 'Tentang', 'Tentang Sekolah'], true))) {
            $profil->update(['judul' => 'Membentuk Generasi Islami & Berprestasi']);
        }

        $judul = $profil->judul ?: $judulDefault;

        $visiMisi = $type === 'visi_misi' ? $profil->visiMisiParts() : null;
        $spmbData = $type === 'spmb' ? $profil->spmbParts() : null;
        $sambutanData = $type === 'sambutan' ? $profil->sambutanParts() : null;

        return view('admin.profil-sekolah.edit', compact('profil', 'type', 'judul', 'visiMisi', 'spmbData', 'sambutanData'));
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

        $profil = ProfilSekolah::where('type', $type)->first();

        // Aturan validasi dinamis (khusus tipe akreditasi, gambar sertifikat wajib diisi jika belum ada)
        $rules = [
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        if ($type === 'visi_misi') {
            $rules['judul'] = 'required|string|max:255';
            $rules['visi'] = 'required|string|max:3000';
            $rules['misi'] = 'required|string|max:10000';
        } elseif ($type === 'sambutan') {
            $rules['judul'] = 'required|string|max:255';
            $rules['sub_judul'] = 'nullable|string|max:255';
            $rules['konten'] = 'required';
        } elseif ($type === 'akreditasi') {
            $rules['judul'] = 'nullable|string|max:255';
            $rules['konten'] = 'nullable';
            $rules['gambar'] = ($profil && $profil->gambar) ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
        } elseif ($type === 'spmb') {
            $rules['judul'] = 'required|string|max:255';
            $rules['nomor_wa'] = 'required|string|max:50';
            $rules['pengantar'] = 'required|string|max:5000';
            $rules['persyaratan_umum'] = 'nullable|string|max:5000';
            $rules['persyaratan_umum_items'] = 'nullable|array';
            $rules['persyaratan_berkas'] = 'nullable|string|max:5000';
            $rules['persyaratan_berkas_items'] = 'nullable|array';
            $rules['alur_pendaftaran'] = 'nullable|string|max:5000';
            $rules['alur_pendaftaran_items'] = 'nullable|array';
            $rules['kuota_items'] = 'nullable|array';
            $rules['kuota_items.*.tahun_ajaran'] = 'nullable|string|max:255';
            $rules['kuota_items.*.status'] = 'nullable|string|max:100';
        } else {
            $rules['judul'] = 'required|string|max:255';
            $rules['konten'] = 'required';
        }

        $request->validate($rules);

        $profil = ProfilSekolah::where('type', $type)->firstOrFail();

        $data = [];
        if ($type === 'visi_misi') {
            $data['judul'] = $request->judul;
            $data['konten'] = "Visi\n".trim($request->visi)."\n\nMisi\n".trim($request->misi);
        } elseif ($type === 'sambutan') {
            $data['judul'] = $request->judul;
            $payload = [
                'sub_judul' => trim((string) ($request->sub_judul ?? 'Kepala Sekolah SD Muhammadiyah Komplek Kolombo')),
                'sambutan_text' => $request->konten,
            ];
            $data['konten'] = json_encode($payload, JSON_UNESCAPED_UNICODE);
        } elseif ($type === 'spmb') {
            $data['judul'] = $request->judul;
            $waNumber = preg_replace('/[^0-9]/', '', (string) $request->nomor_wa);
            if (str_starts_with($waNumber, '0')) {
                $waNumber = '62' . substr($waNumber, 1);
            }

            // Process Persyaratan Umum (Array Repeater or String)
            if ($request->has('persyaratan_umum_items') && is_array($request->persyaratan_umum_items)) {
                $syaratUmumList = [];
                foreach ($request->persyaratan_umum_items as $item) {
                    $val = trim((string) $item);
                    if ($val !== '') {
                        $cleanVal = preg_replace('/^[•\-\*]\s*/u', '', $val);
                        $syaratUmumList[] = '• ' . $cleanVal;
                    }
                }
                $persyaratanUmumStr = implode("\n", $syaratUmumList);
            } else {
                $persyaratanUmumStr = (string) ($request->persyaratan_umum ?? '');
            }

            // Process Persyaratan Berkas (Array Repeater or String)
            if ($request->has('persyaratan_berkas_items') && is_array($request->persyaratan_berkas_items)) {
                $syaratBerkasList = [];
                foreach (array_values($request->persyaratan_berkas_items) as $idx => $item) {
                    $val = trim((string) $item);
                    if ($val !== '') {
                        $cleanVal = preg_replace('/^\d+[.)]\s*/u', '', $val);
                        $syaratBerkasList[] = ($idx + 1) . '. ' . $cleanVal;
                    }
                }
                $persyaratanBerkasStr = implode("\n", $syaratBerkasList);
            } else {
                $persyaratanBerkasStr = (string) ($request->persyaratan_berkas ?? '');
            }

            // Process Alur Pendaftaran (Array Repeater or String)
            if ($request->has('alur_pendaftaran_items') && is_array($request->alur_pendaftaran_items)) {
                $alurList = [];
                foreach (array_values($request->alur_pendaftaran_items) as $idx => $item) {
                    $j = trim((string) ($item['judul'] ?? ''));
                    $d = trim((string) ($item['deskripsi'] ?? ''));
                    if ($j !== '' || $d !== '') {
                        $stepNo = $idx + 1;
                        if ($j !== '' && $d !== '') {
                            $alurList[] = "{$stepNo}. {$j}: {$d}";
                        } else {
                            $alurList[] = "{$stepNo}. " . ($j ?: $d);
                        }
                    }
                }
                $alurStr = implode("\n", $alurList);
            } else {
                $alurStr = (string) ($request->alur_pendaftaran ?? '');
            }

            $kuotaItems = [];
            if ($request->has('kuota_items') && is_array($request->kuota_items)) {
                foreach ($request->kuota_items as $item) {
                    $ta = trim((string) ($item['tahun_ajaran'] ?? ''));
                    if ($ta !== '') {
                        $kuotaItems[] = [
                            'tahun_ajaran' => $ta,
                            'status' => trim((string) ($item['status'] ?? 'Masih Ada Kuota')),
                        ];
                    }
                }
            }
            $spmbPayload = [
                'nomor_wa' => $waNumber,
                'pengantar' => $request->pengantar,
                'persyaratan_umum' => $persyaratanUmumStr,
                'persyaratan_berkas' => $persyaratanBerkasStr,
                'alur_pendaftaran' => $alurStr,
                'kuota_items' => $kuotaItems,
            ];
            $data['konten'] = json_encode($spmbPayload, JSON_UNESCAPED_UNICODE);
        } elseif ($type !== 'akreditasi') {
            $data['judul'] = $request->judul;
            $data['konten'] = $request->konten;
        } else {
            $data['judul'] = $request->judul ?? $profil->judul ?: 'Sertifikat Akreditasi';
            $data['konten'] = $request->konten ?? $profil->konten ?: '';
        }
        
        // Memformat konten teks dan tag HTML
        if ($type !== 'spmb') {
            $data = $formatter->fields($data, [
                'judul' => 'title',
                'konten' => $type === 'visi_misi' ? 'sentence' : 'html',
            ]);
        } else {
            $data['judul'] = $formatter->title($data['judul']);
        }

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
