<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswas';

    protected $fillable = [
        'nama',
        'nis',
        'jenis_kelamin',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kelas',
        'kelas_id',
        'status',
        'tanggal_keluar',
        'sekolah_tujuan',
        'alasan_keluar',
        'tahun_masuk',
        'tahun_lulus',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_keluar' => 'date',
    ];

    protected function nama(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $this->capitalizeEachWord($value),
        );
    }

    protected function tempatLahir(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $this->capitalizeEachWord($value),
        );
    }

    protected function alamat(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $this->capitalizeEachWord($value),
        );
    }

    private function capitalizeEachWord(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = preg_replace('/\s+/u', ' ', trim($value));
        $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        $akronim = ['sd', 'mi', 'smp', 'mts', 'sma', 'smk', 'ma', 'pt', 'cv', 'd3', 'd4', 's1', 's2', 's3'];

        return preg_replace_callback(
            '/\b('.implode('|', $akronim).')\b/iu',
            fn (array $match) => mb_strtoupper($match[0], 'UTF-8'),
            $value
        );
    }

    /**
     * Scope to filter active students.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope to filter alumni.
     */
    public function scopeAlumni($query)
    {
        return $query->where('status', 'alumni');
    }

    public function scopeKeluar($query)
    {
        return $query->where('status', 'keluar');
    }

    /**
     * Scope to filter by class.
     */
    public function scopeKelas($query, $kelas)
    {
        return $query->where(function ($query) use ($kelas) {
            $query->whereHas('kelasData', fn ($q) => $q->where('tingkat', $kelas))
                ->orWhere(function ($legacy) use ($kelas) {
                    $legacy->whereNull('kelas_id')->where('kelas', $kelas);
                });
        });
    }

    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function prestasis()
    {
        return $this->hasMany(Prestasi::class);
    }

    public function ekstrakurikulers()
    {
        return $this->belongsToMany(Ekstrakurikuler::class);
    }

    public function riwayatAkademik()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikanAlumni::class);
    }

    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaanAlumni::class);
    }
}
