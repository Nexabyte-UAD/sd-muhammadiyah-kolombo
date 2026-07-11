<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pesan
 * 
 * Merepresentasikan data pesan masuk (hubungi kami) dari publik/pengunjung website
 * yang dikirim melalui formulir kontak.
 */
class Pesan extends Model
{
    use HasFactory;

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'nama',    // Nama lengkap pengirim pesan
        'email',   // Alamat email aktif pengirim untuk balasan
        'isi',     // Konten/isi pesan atau pertanyaan
        'status',  // Status pembacaan: belum_dibaca (unread) atau dibaca (read)
        'read_at', // Waktu kapan pesan tersebut dibaca oleh admin
    ];

    // Konversi tipe data otomatis
    protected $casts = [
        'read_at' => 'datetime',
    ];
}
