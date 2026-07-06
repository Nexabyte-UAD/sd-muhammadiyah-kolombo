<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->name($value),
        );
    }

    protected function tempatLahir(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    protected function alamat(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->address($value),
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
