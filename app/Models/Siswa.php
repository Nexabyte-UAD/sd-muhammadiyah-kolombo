<?php

namespace App\Models;

use App\Services\IndonesianTextFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Siswa
 * 
 * Merepresentasikan data siswa (aktif, alumni, maupun yang keluar) 
 * pada website sekolah SD Muhammadiyah Komplek Kolombo.
 */
class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    // Menentukan nama tabel yang digunakan oleh model ini
    protected $table = 'siswas';

    // Kolom-kolom yang diperbolehkan untuk diisi secara massal (mass assignment)
    protected $fillable = [
        'nama',
        'nis',
        'jenis_kelamin',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kelas',          // Kolom legacy untuk nama kelas berupa string
        'kelas_id',       // Kunci asing terhubung ke tabel kelas
        'status',         // Status siswa: aktif, alumni, keluar
        'tanggal_keluar', // Tanggal resmi keluar atau lulus
        'sekolah_tujuan', // Sekolah lanjutan (untuk siswa pindahan)
        'alasan_keluar',  // Catatan alasan siswa keluar
        'tahun_masuk',    // Tahun masuk sekolah
        'tahun_lulus',    // Tahun resmi lulus sekolah
        'foto',           // Path berkas foto siswa di penyimpanan
    ];

    // Konversi tipe data otomatis saat diakses (casting)
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Mutator otomatis untuk kolom 'nama'.
     * Memformat teks nama agar mengikuti kaidah penulisan nama Indonesia (Kapital Awal Kata).
     */
    protected function nama(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->name($value),
        );
    }

    /**
     * Mutator otomatis untuk kolom 'tempat_lahir'.
     * Memformat teks tempat lahir menjadi huruf kapital di awal kata (Title Case).
     */
    protected function tempatLahir(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->title($value),
        );
    }

    /**
     * Mutator otomatis untuk kolom 'alamat'.
     * Memformat penulisan alamat (nama jalan, RT/RW, nomor) dengan rapi.
     */
    protected function alamat(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => app(IndonesianTextFormatter::class)->address($value),
        );
    }

    /**
     * Query Scope untuk memfilter siswa yang berstatus aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Query Scope untuk memfilter siswa yang sudah menjadi alumni.
     */
    public function scopeAlumni($query)
    {
        return $query->where('status', 'alumni');
    }

    /**
     * Query Scope untuk memfilter siswa yang sudah keluar/pindah sekolah.
     */
    public function scopeKeluar($query)
    {
        return $query->where('status', 'keluar');
    }

    /**
     * Query Scope untuk memfilter siswa berdasarkan tingkatan kelas.
     * Mendukung pencarian baik relasi dinamis baru (kelas_id) maupun data lama (kolom string kelas).
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

    /**
     * Hubungan Relasi (Belongs To) ke model Kelas.
     * Mengembalikan data kelas dari siswa tersebut.
     */
    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Hubungan Relasi (Has Many) ke model Prestasi.
     * Menghubungkan siswa dengan penghargaan/prestasi yang pernah diraih.
     */
    public function prestasis()
    {
        return $this->hasMany(Prestasi::class);
    }

    /**
     * Hubungan Relasi (Belongs To Many) ke model Ekstrakurikuler.
     * Menghubungkan siswa ke kegiatan ekstra kurikuler yang diikuti.
     */
    public function ekstrakurikulers()
    {
        return $this->belongsToMany(Ekstrakurikuler::class);
    }

    /**
     * Hubungan Relasi (Has Many) ke model RiwayatAkademik.
     * Mencatat sejarah kenaikan kelas, kelulusan, dan perpindahan status siswa.
     */
    public function riwayatAkademik()
    {
        return $this->hasMany(RiwayatAkademik::class);
    }

    /**
     * Hubungan Relasi (Has Many) ke model RiwayatPendidikanAlumni.
     * Digunakan khusus untuk alumni dalam mendata riwayat pendidikan lanjutan (SMP/SMA/PT).
     */
    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikanAlumni::class);
    }

    /**
     * Hubungan Relasi (Has Many) ke model RiwayatPekerjaanAlumni.
     * Digunakan khusus untuk alumni dalam mendata riwayat pekerjaan/profesi saat ini.
     */
    public function riwayatPekerjaan()
    {
        return $this->hasMany(RiwayatPekerjaanAlumni::class);
    }
}
