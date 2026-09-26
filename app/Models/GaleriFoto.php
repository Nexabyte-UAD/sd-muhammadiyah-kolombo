<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriFoto extends Model
{
    protected $table = 'galeri_fotos';

    protected $fillable = [
        'judul',
        'kategori',
        'foto',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const KATEGORI = [
        'Kegiatan Sekolah',
        'Pembelajaran & Kelas',
        'Lomba & Prestasi',
        'Ekstrakurikuler',
        'Fasilitas & Acara',
    ];
}
