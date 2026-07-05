<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    use HasFactory;

    public const KATEGORI = [
        'akademik' => 'Akademik',
        'nonakademik' => 'Nonakademik',
        'keagamaan' => 'Keagamaan',
    ];

    protected $fillable = [
        'judul',
        'kategori',
        'siswa_id',
        'nama_siswa',
        'prestasi_medali',
        'penyelenggara',
        'deskripsi',
        'tanggal',
        'gambar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
