<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RiwayatPekerjaanAlumni extends Model
{
    protected $table = 'riwayat_pekerjaan_alumni';

    protected $fillable = ['pekerjaan', 'perusahaan', 'tahun_mulai', 'tahun_selesai'];

    protected function pekerjaan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    protected function perusahaan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }
}
