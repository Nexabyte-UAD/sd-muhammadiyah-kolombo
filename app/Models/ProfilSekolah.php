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
}
