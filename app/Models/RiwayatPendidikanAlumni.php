<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Model RiwayatPendidikanAlumni
 * 
 * Menyimpan riwayat pendidikan lanjutan (SMP, SMA, Perguruan Tinggi, dll.) 
 * dari alumni SD Muhammadiyah Komplek Kolombo untuk penelusuran lulusan (tracer study).
 */
class RiwayatPendidikanAlumni extends Model
{
    // Nama tabel database
    protected $table = 'riwayat_pendidikan_alumni';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = ['jenjang', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_selesai'];

    /**
     * Mutator otomatis kolom 'jenjang'.
     * Merapikan penulisan tingkat jenjang pendidikan (misal: "smp negeri 1" menjadi "SMP Negeri 1").
     */
    protected function jenjang(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    /**
     * Mutator otomatis kolom 'institusi'.
     * Merapikan penulisan nama sekolah/kampus.
     */
    protected function institusi(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    /**
     * Mutator otomatis kolom 'jurusan'.
     * Merapikan penulisan nama jurusan/program studi (misal: "ipa" menjadi "IPA").
     */
    protected function jurusan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }
}
