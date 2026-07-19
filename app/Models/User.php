<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 * 
 * Merepresentasikan akun pengguna (Administrator / Staff) yang dapat login
 * ke panel admin dan mengelola data sekolah SD Muhammadiyah Komplek Kolombo.
 */
#[Fillable(['name', 'email', 'username', 'password', 'role', 'last_login_at', 'last_login_ip'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Konversi tipe data otomatis saat kolom diakses.
     * Mengatur tanggal verifikasi email, hashing password otomatis, dan waktu login terakhir.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Mengirim notifikasi pengaturan ulang password dengan identitas sekolah.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Hubungan Relasi (Has Many) ke model Berita.
     * Mengembalikan daftar berita yang ditulis/diterbitkan oleh akun pengguna ini.
     */
    public function beritas()
    {
        return $this->hasMany(Berita::class);
    }

    /**
     * Hubungan Relasi (Has Many) ke model ActivityLog.
     * Mencatat riwayat log aktivitas yang dilakukan oleh akun pengguna ini saat mengelola panel admin.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
