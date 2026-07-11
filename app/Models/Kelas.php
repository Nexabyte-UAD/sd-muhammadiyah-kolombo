<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Kelas
 * 
 * Merepresentasikan data tingkatan kelas dinamis pada SD Muhammadiyah Komplek Kolombo.
 */
class Kelas extends Model
{
    // Nama tabel di database
    protected $table = 'kelas';

    // Kolom-kolom yang dapat diisi massal
    protected $fillable = ['tingkat', 'urutan', 'tahun_ajaran', 'kapasitas', 'jurusan', 'wali_kelas_id'];

    /**
     * Memeriksa apakah kapasitas kelas saat ini sudah penuh.
     * Digunakan sebagai validasi saat menambah siswa baru atau menaikkan kelas.
     * 
     * @param  int|null  $abaikanSiswaId  ID siswa yang diabaikan saat perhitungan (misal saat edit data siswa itu sendiri).
     * @return bool                       True jika kapasitas sudah penuh, False jika masih ada slot kosong.
     */
    public function sudahPenuh(?int $abaikanSiswaId = null): bool
    {
        // Jika kapasitas tidak diset (null/0), dianggap tidak terbatas
        if (! $this->kapasitas) {
            return false;
        }

        // Hitung jumlah siswa aktif di kelas ini saat ini
        $jumlah = $this->siswas()
            ->where('status', 'aktif')
            ->when($abaikanSiswaId, fn ($query) => $query->whereKeyNot($abaikanSiswaId))
            ->count();

        return $jumlah >= $this->kapasitas;
    }

    /**
     * Mutator otomatis kolom 'tingkat'.
     * Menjamin format penulisan tingkat kelas konsisten (misal: "Kelas 3A").
     */
    protected function tingkat(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalizeLabel($value, true),
        );
    }

    /**
     * Mutator otomatis kolom 'jurusan' (atau pengelompokan program kelas).
     */
    protected function jurusan(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => self::normalizeLabel($value),
        );
    }

    /**
     * Helper statis untuk merapikan teks label (tingkat/jurusan).
     * Merapikan spasi ganda, mengubah ke Title Case (Kapital Awal Kata),
     * dan memastikan akhiran romawi/abjad kelas ditulis dalam huruf kapital (misal: "3a" menjadi "3A").
     */
    public static function normalizeLabel(?string $value, bool $uppercaseClassSuffix = false): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        // Hapus spasi ganda
        $value = preg_replace('/\s+/u', ' ', trim($value));
        // Ubah format teks ke Title Case
        $value = mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');

        // Jika diset untuk akhiran kelas, jadikan huruf kapital (misal: 1a -> 1A, 2b -> 2B)
        if ($uppercaseClassSuffix) {
            $value = preg_replace_callback(
                '/\b(\d+)([a-z])\b/iu',
                fn (array $match) => $match[1].mb_strtoupper($match[2], 'UTF-8'),
                $value
            );
        }

        return $value;
    }

    /**
     * Hubungan Relasi (Belongs To) ke model GuruStaff.
     * Mengembalikan data wali kelas (Guru) yang memimpin kelas ini.
     */
    public function waliKelas()
    {
        return $this->belongsTo(GuruStaff::class, 'wali_kelas_id');
    }

    /**
     * Hubungan Relasi (Has Many) ke model Siswa.
     * Mengembalikan daftar seluruh siswa yang terdaftar di kelas ini.
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}
