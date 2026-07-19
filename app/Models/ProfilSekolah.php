<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model ProfilSekolah
 * 
 * Merepresentasikan data profil statis sekolah seperti deskripsi tentang sekolah,
 * sambutan kepala sekolah, visi & misi, serta informasi akreditasi sekolah.
 */
class ProfilSekolah extends Model
{
    use HasFactory;

    // Tipe profil sekolah yang didukung
    public const TYPES = [
        'tentang',    // Halaman Tentang Sekolah
        'sambutan',   // Halaman Sambutan Kepala Sekolah
        'visi_misi',  // Halaman Visi & Misi Sekolah
        'akreditasi', // Halaman Info Akreditasi Sekolah
    ];

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'type',   // Tipe profil (tentang, sambutan, visi_misi, akreditasi)
        'judul',  // Judul halaman/profil
        'konten', // Konten teks/HTML lengkap profil sekolah
        'gambar'  // Foto/ilustrasi pendukung profil sekolah
    ];

    public function visiMisiParts(): array
    {
        $content = (string) ($this->konten ?? '');
        $plainText = preg_replace('/<\s*br\s*\/?>/i', "\n", $content);
        $plainText = preg_replace('/<\/\s*(p|div|li|h[1-6])\s*>/i', "\n", $plainText);
        $plainText = html_entity_decode(strip_tags($plainText), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $lines = preg_split('/\R/u', str_replace("\xC2\xA0", ' ', $plainText)) ?: [];
        $visi = [];
        $misi = [];
        $inMission = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^misi(?:\s+(?:sekolah|kami))?\s*:?$/iu', $line)) {
                $inMission = true;
                continue;
            }

            if (!$inMission && preg_match('/^visi(?:\s+(?:sekolah|kami))?\s*:?$/iu', $line)) {
                continue;
            }

            if ($inMission) {
                $misi[] = preg_replace('/^(?:\d+[.)]|[-*])\s*/u', '', $line);
            } else {
                $visi[] = $line;
            }
        }

        return [
            'visi' => trim(implode("\n", $visi)),
            'misi' => array_values(array_filter(array_map('trim', $misi))),
        ];
    }
}
