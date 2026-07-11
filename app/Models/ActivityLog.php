<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model ActivityLog
 * 
 * Digunakan untuk mencatat log audit (audit trail) dari segala aktivitas
 * yang dilakukan oleh administrator/staff di panel admin (tambah, edit, hapus data).
 */
class ActivityLog extends Model
{
    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',     // ID administrator yang melakukan aksi
        'action_type', // Jenis aksi: create, update, delete, login, logout
        'module',      // Nama modul/tabel yang diakses (misal: Siswa, Berita)
        'description'  // Penjelasan detail tentang aksi (misal: "Menambahkan siswa baru bernama Budi")
    ];

    /**
     * Hubungan Relasi (Belongs To) ke model User.
     * Mengembalikan data admin (User) pelaku aktivitas ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
