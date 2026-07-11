<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model RiwayatAkademik
 * 
 * Mencatat sejarah akademik siswa, termasuk kenaikan tingkat kelas, kelulusan,
 * atau perpindahan status lainnya beserta administrator yang memprosesnya.
 */
class RiwayatAkademik extends Model
{
    // Nama tabel database
    protected $table = 'riwayat_akademik';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'siswa_id',       // ID siswa terkait
        'tahun_ajaran',   // Tahun ajaran saat kenaikan/kelulusan diproses (misal: 2023/2024)
        'kelas_asal',     // Kelas siswa sebelum kenaikan (misal: Kelas 3A atau NULL jika baru masuk)
        'kelas_tujuan',   // Kelas siswa setelah kenaikan (misal: Kelas 4A atau status kelulusan)
        'keputusan',      // Hasil keputusan (Naik Kelas, Tinggal Kelas, Lulus, Keluar)
        'catatan',        // Keterangan atau alasan (misal: pindah sekolah, dll)
        'diproses_oleh',  // ID admin (User) yang melakukan eksekusi/proses ini
        'tanggal_proses', // Waktu eksekusi proses tersebut
    ];

    // Konversi tipe data otomatis
    protected $casts = [
        'tanggal_proses' => 'datetime',
    ];

    /**
     * Hubungan Relasi (Belongs To) ke model Siswa.
     * Mengembalikan data siswa yang memiliki riwayat akademik ini.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Hubungan Relasi (Belongs To) ke model User.
     * Mengembalikan data administrator (User) yang mengeksekusi kenaikan/kelulusan siswa ini.
     */
    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
