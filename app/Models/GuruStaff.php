<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model GuruStaff
 * 
 * Merepresentasikan data Guru dan Tenaga Kependidikan (Staf)
 * pada SD Muhammadiyah Komplek Kolombo.
 */
class GuruStaff extends Model
{
    use HasFactory;

    // Pilihan Jenis Kelamin
    public const JENIS_KELAMIN = [
        'laki_laki' => 'Laki-laki',
        'perempuan' => 'Perempuan',
    ];

    // Pilihan Status Kepegawaian
    public const STATUS_KEPEGAWAIAN = [
        'PNS' => 'PNS',
        'PPPK' => 'PPPK',
        'Honorer' => 'Honorer',
        'GTY/GTT' => 'GTY/GTT',
    ];

    // Pilihan Pendidikan Terakhir
    public const PENDIDIKAN_TERAKHIR = [
        'SD' => 'SD',
        'SMP' => 'SMP',
        'SMA' => 'SMA',
        'S1' => 'S1',
        'S2' => 'S2',
        'S3' => 'S3',
    ];

    // Pilihan Agama
    public const AGAMA = [
        'Islam' => 'Islam',
        'Kristen' => 'Kristen',
        'Katolik' => 'Katolik',
        'Hindu' => 'Hindu',
        'Buddha' => 'Buddha',
        'Konghucu' => 'Konghucu',
    ];

    // Nama tabel database
    protected $table = 'guru_staffs';

    // Kolom-kolom yang dapat diisi secara massal
    protected $fillable = [
        'tipe',                  // Tipe pegawai: guru atau staff
        'nama',                  // Nama lengkap guru/staf
        'jenis_kelamin',         // Jenis kelamin
        'jabatan',               // Jabatan di sekolah (misal: Kepala Sekolah, Guru Kelas)
        'bidang_tugas',          // Tugas spesifik (misal: Wali Kelas 3, Kebersihan)
        'nip',                   // Nomor Induk Pegawai (jika ada)
        'status_kepegawaian',    // Status kerja (PNS, PPPK, Honorer, dll)
        'pendidikan_terakhir',   // Pendidikan terakhir (S1, S2, dll)
        'agama',                 // Agama
        'foto',                  // File foto di storage
    ];
}
