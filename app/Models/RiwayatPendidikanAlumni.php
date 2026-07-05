<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikanAlumni extends Model
{
    protected $table = 'riwayat_pendidikan_alumni';
    protected $fillable = ['jenjang', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_selesai'];

    protected function jenjang(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $this->rapikan($value));
    }

    protected function institusi(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $this->rapikan($value));
    }

    protected function jurusan(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $this->rapikan($value));
    }

    private function rapikan(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        $value = mb_convert_case(preg_replace('/\s+/u', ' ', trim($value)), MB_CASE_TITLE, 'UTF-8');

        return preg_replace_callback('/\b(Sd|Mi|Smp|Mts|Sma|Smk|Ma|Pt|Cv|D3|D4|S1|S2|S3)\b/u',
            fn ($match) => mb_strtoupper($match[0], 'UTF-8'), $value);
    }
}
