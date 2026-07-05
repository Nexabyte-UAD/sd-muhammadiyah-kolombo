<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RiwayatPekerjaanAlumni extends Model
{
    protected $table = 'riwayat_pekerjaan_alumni';
    protected $fillable = ['pekerjaan', 'perusahaan', 'tahun_mulai', 'tahun_selesai'];

    protected function pekerjaan(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $this->rapikan($value));
    }

    protected function perusahaan(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $this->rapikan($value));
    }

    private function rapikan(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $value = mb_convert_case(preg_replace('/\s+/u', ' ', trim($value)), MB_CASE_TITLE, 'UTF-8');

        return preg_replace_callback('/\b(Pt|Cv)\b/u',
            fn ($match) => mb_strtoupper($match[0], 'UTF-8'), $value);
    }
}
