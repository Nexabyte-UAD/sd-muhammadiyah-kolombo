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
        'spmb',       // Halaman Informasi SPMB / PPDB
    ];

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'type',   // Tipe profil (tentang, sambutan, visi_misi, akreditasi, spmb)
        'judul',  // Judul halaman/profil
        'konten', // Konten teks/HTML lengkap profil sekolah
        'gambar'  // Foto/ilustrasi pendukung profil sekolah
    ];

    public function spmbParts(): array
    {
        $default = [
            'tahun_ajaran' => '2026/2027',
            'status_pendaftaran' => 'Pendaftaran Masih Dibuka',
            'nomor_wa' => '6281234567890',
            'pengantar' => 'Selamat datang di Sistem Penerimaan Peserta Didik Baru (SPMB) SD Muhammadiyah Komplek Kolombo Tahun Ajaran 2026/2027. Kami berkomitmen untuk menyelenggarakan pendidikan dasar berkarakter Islami, berprestasi akademik, dan mengembangkan potensi siswa secara holistik dalam lingkungan belajar yang aman dan nyaman.',
            'persyaratan_umum' => "• Kriteria Usia: Calon siswa berusia minimal 6 tahun pada tanggal 1 Juli tahun berjalan.\n• Kesiapan Belajar: Memiliki kesiapan fisik dan mental untuk mengikuti kegiatan belajar mengajar sekolah dasar.\n• Komitmen Orang Tua: Orang tua/wali siswa bersedia mendukung program dan tata tertib sekolah.",
            'persyaratan_berkas' => "1. Fotokopi Akta Kelahiran: 2 lembar.\n2. Fotokopi Kartu Keluarga (KK): 2 lembar.\n3. Pas Foto Terbaru: Ukuran 3x4 cm (3 lembar).\n4. Fotokopi Ijazah / Sertifikat TK: 1 lembar (jika ada).",
            'alur_pendaftaran' => "1. Pengisian Formulir: Orang tua/wali murid mengisi formulir pendaftaran fisik di Sekretariat PPDB sekolah atau mendaftar via WhatsApp panitia.\n2. Penyerahan Berkas: Menyerahkan dokumen persyaratan administrasi lengkap ke panitia pendaftaran untuk diverifikasi.\n3. Pemetaan Kesiapan: Calon siswa mengikuti kegiatan ramah anak untuk pemetaan kesiapan belajar (bukan tes akademik seleksi gugur).\n4. Daftar Ulang: Melakukan konfirmasi daftar ulang administrasi dan penerimaan perlengkapan seragam siswa.",
            'kuota_items' => [
                ['tahun_ajaran' => 'Tahun Ajaran 2026/2027', 'status' => 'Masih Ada Kuota'],
                ['tahun_ajaran' => 'Tahun Ajaran 2029/2030', 'status' => 'Masih Ada Kuota'],
                ['tahun_ajaran' => 'Tahun Ajaran 2030/2031', 'status' => 'Masih Ada Kuota'],
                ['tahun_ajaran' => 'Tahun Ajaran 2027/2028', 'status' => 'Ditutup'],
                ['tahun_ajaran' => 'Tahun Ajaran 2028/2029', 'status' => 'Ditutup'],
            ],
        ];

        if (!$this->konten) {
            $result = $default;
        } else {
            $decoded = json_decode($this->konten, true);
            if (is_array($decoded)) {
                $result = array_merge($default, $decoded);
            } else {
                $result = $default;
                $result['pengantar'] = $this->konten;
            }
        }

        if (empty($result['kuota_items']) && !empty($result['kuota_pendaftaran'])) {
            $items = [];
            $lines = array_filter(array_map('trim', explode("\n", $result['kuota_pendaftaran'])));
            foreach ($lines as $line) {
                $parts = explode(':', $line, 2);
                $items[] = [
                    'tahun_ajaran' => trim($parts[0] ?? $line),
                    'status' => trim($parts[1] ?? 'Masih Ada Kuota'),
                ];
            }
            $result['kuota_items'] = $items;
        }

        // Hitung otomatis Tahun Ajaran Aktif dan Status Pendaftaran dari daftar kuota_items
        $items = $result['kuota_items'] ?? [];
        $activeItem = null;
        $hasOpen = false;

        foreach ($items as $item) {
            $isClosed = preg_match('/(tutup|ditutup|full|habis)/i', $item['status'] ?? '');
            if (!$isClosed) {
                $hasOpen = true;
                if (!$activeItem) {
                    $activeItem = $item;
                }
            }
        }

        if (!$activeItem && !empty($items)) {
            $activeItem = $items[0];
        }

        $result['tahun_ajaran'] = $activeItem['tahun_ajaran'] ?? '2026/2027';
        $result['tahun_ajaran_clean'] = trim(preg_replace('/^tahun\s+ajaran\s*/i', '', $result['tahun_ajaran']));
        $result['status_pendaftaran'] = $hasOpen ? 'Pendaftaran Masih Dibuka' : 'Pendaftaran Ditutup';

        // Parse array items untuk Repeater UI di Admin Panel
        $syaratUmumLines = array_values(array_filter(array_map(function ($line) {
            return preg_replace('/^[•\-\*]\s*/u', '', trim($line));
        }, explode("\n", $result['persyaratan_umum'] ?? ''))));
        $result['persyaratan_umum_items'] = $syaratUmumLines;

        $berkasLines = array_values(array_filter(array_map(function ($line) {
            return preg_replace('/^\d+[.)]\s*/u', '', trim($line));
        }, explode("\n", $result['persyaratan_berkas'] ?? ''))));
        $result['persyaratan_berkas_items'] = $berkasLines;

        $alurLines = array_values(array_filter(array_map('trim', explode("\n", $result['alur_pendaftaran'] ?? ''))));
        $alurItems = [];
        foreach ($alurLines as $idx => $line) {
            if (preg_match('/^\d+[.)]\s*(.+?):\s*(.+)$/u', $line, $m)) {
                $alurItems[] = [
                    'judul' => trim($m[1]),
                    'deskripsi' => trim($m[2]),
                ];
            } else {
                $cleanLine = preg_replace('/^\d+[.)]\s*/u', '', $line);
                $alurItems[] = [
                    'judul' => 'Langkah ' . ($idx + 1),
                    'deskripsi' => $cleanLine,
                ];
            }
        }
        $result['alur_pendaftaran_items'] = $alurItems;

        return $result;
    }

    public function sambutanParts(): array
    {
        $defaultSubJudul = 'Kepala Sekolah SD Muhammadiyah Komplek Kolombo';
        
        if (!$this->konten) {
            return [
                'nama' => $this->judul ?: 'Drs. H. Ahmad Dahlan, M.Pd.',
                'sub_judul' => $defaultSubJudul,
                'konten' => '',
            ];
        }

        $decoded = json_decode($this->konten, true);
        if (is_array($decoded) && array_key_exists('sambutan_text', $decoded)) {
            return [
                'nama' => $this->judul ?: 'Drs. H. Ahmad Dahlan, M.Pd.',
                'sub_judul' => $decoded['sub_judul'] ?? $defaultSubJudul,
                'konten' => $decoded['sambutan_text'] ?? '',
            ];
        }

        return [
            'nama' => $this->judul ?: 'Drs. H. Ahmad Dahlan, M.Pd.',
            'sub_judul' => $defaultSubJudul,
            'konten' => $this->konten,
        ];
    }

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
