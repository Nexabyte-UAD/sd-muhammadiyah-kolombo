<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = ['tingkat', 'urutan', 'tahun_ajaran', 'kapasitas', 'jurusan', 'wali_kelas_id'];

    public function sudahPenuh(?int $abaikanSiswaId = null): bool
    {
        if (! $this->kapasitas) {
            return false;
        }

        $jumlah = $this->siswas()
            ->where('status', 'aktif')
            ->when($abaikanSiswaId, fn ($query) => $query->whereKeyNot($abaikanSiswaId))
            ->count();

        return $jumlah >= $this->kapasitas;
    }

    protected function tingkat(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalizeLabel($value, true),
        );
    }

    protected function jurusan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalizeLabel($value),
        );
    }

    public static function normalizeLabel(?string $value, bool $uppercaseClassSuffix = false): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value));
        $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');

        if ($uppercaseClassSuffix) {
            $value = preg_replace_callback(
                '/\b(\d+)([a-z])\b/iu',
                fn (array $match) => $match[1].mb_strtoupper($match[2], 'UTF-8'),
                $value
            );
        }

        return $value;
    }

    public function waliKelas()
    {
        return $this->belongsTo(GuruStaff::class, 'wali_kelas_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}
