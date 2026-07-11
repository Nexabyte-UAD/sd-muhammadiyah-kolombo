<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Setting
 * 
 * Merepresentasikan konfigurasi dinamis sistem/aplikasi (Key-Value)
 * seperti nama sekolah, alamat, kontak, dan gambar latar hero beranda.
 */
class Setting extends Model
{
    use HasFactory;

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'key',   // Nama/kata kunci konfigurasi (misal: nama_sekolah, hero_image)
        'value', // Nilai atau isi dari konfigurasi
        'type'   // Tipe data nilai (misal: text, image) untuk formatting
    ];
}
