<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuruMenulis extends Model
{
    protected $table = 'guru_menulis';

    protected $fillable = [
        'judul',
        'slug',
        'penulis',
        'guru_staff_id',
        'kategori',
        'gambar',
        'isi',
        'status',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const KATEGORI = [
        'Opini & Artikel',
        'Karya Tulis Guru',
        'Inovasi Pembelajaran',
        'Literasi & Edukasi',
        'Pengalaman Mengajar',
    ];

    public function guruStaff()
    {
        return $this->belongsTo(GuruStaff::class, 'guru_staff_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul) . '-' . Str::random(5);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('judul') && !$model->isDirty('slug')) {
                $model->slug = Str::slug($model->judul) . '-' . Str::random(5);
            }
        });
    }
}
