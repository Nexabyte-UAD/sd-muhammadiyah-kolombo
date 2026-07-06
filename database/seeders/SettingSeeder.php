<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'nama_sekolah', 'value' => 'SD Muhammadiyah Kolombo', 'type' => 'text'],
            ['key' => 'alamat', 'value' => 'Jl. Rajawali No. 10, Demangan Baru, Depok, Sleman, Yogyakarta', 'type' => 'text'],
            ['key' => 'telepon', 'value' => '(0274) 585755', 'type' => 'text'],
            ['key' => 'email', 'value' => 'sdmuhkkolombo@gmail.com', 'type' => 'text'],
            ['key' => 'facebook', 'value' => 'https://facebook.com', 'type' => 'url'],
            ['key' => 'instagram', 'value' => 'https://instagram.com', 'type' => 'url'],
            ['key' => 'tiktok', 'value' => 'https://tiktok.com', 'type' => 'url'],
            ['key' => 'youtube', 'value' => 'https://youtube.com', 'type' => 'url'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
