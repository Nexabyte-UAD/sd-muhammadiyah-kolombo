<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['tingkat', 'jurusan', 'wali_kelas_id'];

    public function waliKelas()
    {
        return $this->belongsTo(GuruStaff::class, 'wali_kelas_id');
    }
}
