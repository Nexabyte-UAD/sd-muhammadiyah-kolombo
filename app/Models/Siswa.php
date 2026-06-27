<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswas';

    protected $fillable = [
        'nama',
        'nis',
        'nisn',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kelas',
        'status',
        'tahun_masuk',
        'tahun_lulus',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Scope to filter active students.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope to filter alumni.
     */
    public function scopeAlumni($query)
    {
        return $query->where('status', 'alumni');
    }

    /**
     * Scope to filter by class.
     */
    public function scopeKelas($query, $kelas)
    {
        return $query->where('kelas', $kelas);
    }
}
