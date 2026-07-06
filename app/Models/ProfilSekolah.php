<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilSekolah extends Model
{
    use HasFactory;

    public const TYPES = [
        'tentang',
        'sambutan',
        'visi_misi',
        'akreditasi',
    ];

    protected $fillable = ['type', 'judul', 'konten', 'gambar'];
}
