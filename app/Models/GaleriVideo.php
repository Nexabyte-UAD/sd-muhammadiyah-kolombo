<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaleriVideo extends Model
{
    protected $table = 'galeri_videos';

    protected $fillable = [
        'judul',
        'kategori',
        'youtube_url',
        'thumbnail',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const KATEGORI = [
        'Kegiatan Sekolah',
        'Profil & Liputan',
        'Lomba & Pentas Seni',
        'Ekstrakurikuler',
        'Dokumentasi Acara',
    ];

    /**
     * Extract YouTube Video ID from any standard YouTube URL, Shorts, live, or embed link.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->youtube_url)) {
            return null;
        }

        $url = trim($this->youtube_url);

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts|live)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get default YouTube HQ thumbnail URL if custom thumbnail is empty.
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }

        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }

        return asset('assets/images/no-image-available.jpg');
    }

    /**
     * Get valid embed URL for iframe player.
     */
    public function getEmbedUrlAttribute(): string
    {
        if ($this->youtube_id) {
            return "https://www.youtube-nocookie.com/embed/{$this->youtube_id}?autoplay=1";
        }

        return $this->youtube_url;
    }
}
