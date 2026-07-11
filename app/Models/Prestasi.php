<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Prestasi
 * 
 * Merepresentasikan data prestasi atau penghargaan yang diraih oleh siswa sekolah
 * baik akademik, nonakademik, maupun keagamaan.
 */
class Prestasi extends Model
{
    use HasFactory;

    // Kategori Prestasi
    public const KATEGORI = [
        'akademik' => 'Akademik',
        'nonakademik' => 'Nonakademik',
        'keagamaan' => 'Keagamaan',
    ];

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'judul',           // Nama lomba/kejuaraan yang dimenangkan
        'kategori',        // Kategori (akademik, non-akademik, keagamaan)
        'siswa_id',        // Kunci asing yang berelasi ke tabel siswa (jika terdaftar di sistem)
        'nama_siswa',      // Nama siswa peraih prestasi (opsional jika data siswa tidak terdaftar)
        'prestasi_medali', // Juara yang diraih (Juara 1, Emas, dll)
        'penyelenggara',   // Instansi penyelenggara lomba
        'deskripsi',       // Deskripsi perlombaan atau detail prestasi
        'tanggal',         // Tanggal diraihnya prestasi
        'gambar',          // Foto piala/medali/sertifikat siswa
    ];

    // Casting tipe data otomatis
    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Hubungan Relasi (Belongs To) ke model Siswa.
     * Mengembalikan data siswa yang memenangkan prestasi ini.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
