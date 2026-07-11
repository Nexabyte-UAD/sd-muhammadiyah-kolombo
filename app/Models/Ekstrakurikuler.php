<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Ekstrakurikuler
 * 
 * Merepresentasikan data kegiatan ekstrakurikuler (kegiatan non-akademik di luar jam belajar)
 * pada SD Muhammadiyah Komplek Kolombo.
 */
class Ekstrakurikuler extends Model
{
    use HasFactory;

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'nama',      // Nama kegiatan ekstrakurikuler (misal: Hizbul Wathan, Tapak Suci)
        'deskripsi', // Keterangan atau profil singkat ekstrakurikuler
        'pembina',   // Nama guru atau pelatih pembimbing ekstrakurikuler
        'jadwal',    // Hari dan jam pelaksanaan latihan
        'foto'       // Berkas foto dokumentasi kegiatan ekstrakurikuler
    ];

    /**
     * Hubungan Relasi (Belongs To Many) ke model Siswa.
     * Mengembalikan daftar siswa yang mengikuti kegiatan ekstrakurikuler ini (Many-to-Many).
     */
    public function siswas()
    {
        return $this->belongsToMany(Siswa::class);
    }
}
