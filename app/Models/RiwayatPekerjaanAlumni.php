<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Model RiwayatPekerjaanAlumni
 * 
 * Menyimpan riwayat karir, pekerjaan, atau profesi alumni 
 * untuk melacak persebaran profesi alumni SD Muhammadiyah Komplek Kolombo.
 */
class RiwayatPekerjaanAlumni extends Model
{
    // Nama tabel database
    protected $table = 'riwayat_pekerjaan_alumni';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = ['pekerjaan', 'perusahaan', 'tahun_mulai', 'tahun_selesai'];

    /**
     * Mutator otomatis kolom 'pekerjaan'.
     * Memformat penulisan jenis pekerjaan (misal: "pegawai swasta" menjadi "Pegawai Swasta").
     */
    protected function pekerjaan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    /**
     * Mutator otomatis kolom 'perusahaan'.
     * Memformat penulisan nama tempat bekerja/perusahaan.
     */
    protected function perusahaan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }
}
