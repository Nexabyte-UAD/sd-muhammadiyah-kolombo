<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanAlumni extends Model
{
    protected $table = 'riwayat_pendidikan_alumni';

    protected $fillable = ['jenjang', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_selesai'];

    protected function jenjang(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    protected function institusi(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    protected function jurusan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }
}
