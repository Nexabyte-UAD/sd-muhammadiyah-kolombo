<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatAkademik extends Model
{
    protected $table = 'riwayat_akademik';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran',
        'kelas_asal',
        'kelas_tujuan',
        'keputusan',
        'catatan',
        'diproses_oleh',
        'tanggal_proses',
    ];

    protected $casts = [
        'tanggal_proses' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
