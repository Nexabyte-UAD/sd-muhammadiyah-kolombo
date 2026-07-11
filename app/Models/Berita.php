<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Berita
 * 
 * Merepresentasikan artikel berita, pengumuman, atau artikel sekolah
 * yang diterbitkan oleh administrator.
 */
class Berita extends Model
{
    use HasFactory;

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'judul',   // Judul artikel/berita sekolah
        'isi',     // Konten/isi lengkap berita
        'gambar',  // Berkas foto ilustrasi berita
        'tanggal', // Tanggal publikasi berita
        'status',  // Status tayang: dipublikasikan (published) atau draf (draft)
        'user_id'  // ID admin pembuat/penulis berita
    ];

    // Konversi tipe data otomatis saat diakses
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Hubungan Relasi (Belongs To) ke model User.
     * Mengembalikan data admin (User) yang menulis berita ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
