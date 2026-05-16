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
            ['key' => 'nama_sekolah', 'value' => 'SD Muhammadiah Kolombo', 'type' => 'text'],
            ['key' => 'logo', 'value' => null, 'type' => 'image'],
            ['key' => 'alamat', 'value' => 'Jl. Kolombo No. 123, Yogyakarta', 'type' => 'text'],
            ['key' => 'telepon', 'value' => '+62 274 1234567', 'type' => 'text'],
            ['key' => 'email', 'value' => 'info@sdmuhkolombo.sch.id', 'type' => 'text'],
            ['key' => 'facebook', 'value' => 'https://facebook.com', 'type' => 'url'],
            ['key' => 'instagram', 'value' => 'https://instagram.com', 'type' => 'url'],
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
