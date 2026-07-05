<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruStaff extends Model
{
    use HasFactory;

    public const JENIS_KELAMIN = [
        'laki_laki' => 'Laki-laki',
        'perempuan' => 'Perempuan',
    ];

    public const STATUS_KEPEGAWAIAN = [
        'PNS' => 'PNS',
        'PPPK' => 'PPPK',
        'Honorer' => 'Honorer',
        'GTY/GTT' => 'GTY/GTT',
    ];

    public const PENDIDIKAN_TERAKHIR = [
        'SD' => 'SD',
        'SMP' => 'SMP',
        'SMA' => 'SMA',
        'S1' => 'S1',
        'S2' => 'S2',
        'S3' => 'S3',
    ];

    public const AGAMA = [
        'Islam' => 'Islam',
        'Kristen' => 'Kristen',
        'Katolik' => 'Katolik',
        'Hindu' => 'Hindu',
        'Buddha' => 'Buddha',
        'Konghucu' => 'Konghucu',
    ];

    protected $table = 'guru_staffs';

    protected $fillable = [
        'tipe',
        'nama',
        'jenis_kelamin',
        'jabatan',
        'bidang_tugas',
        'nip',
        'status_kepegawaian',
        'pendidikan_terakhir',
        'agama',
        'foto',
    ];
}
