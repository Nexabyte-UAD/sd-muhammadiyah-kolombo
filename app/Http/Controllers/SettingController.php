<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request, IndonesianTextFormatter $formatter)
    {
        $data = $request->except(['_token', '_method', 'logo', 'hero_image', 'hero_image_2', 'hero_image_3']); // Ambil semua kecuali token dan file
        $data = $formatter->fields($data, [
            'nama_sekolah' => 'title',
            'beranda_profil_judul' => 'title',
            'kepsek_nama' => 'name',
            'beranda_profil_teks' => 'sentence',
            'kepsek_sambutan' => 'sentence',
            'telepon' => 'phone',
            'alamat' => 'address',
        ]);

        // Update text/url settings
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Handle logo file upload
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            $path = $request->file('logo')->store('settings', 'public');

            // Hapus logo lama jika ada
            $oldSetting = Setting::where('key', 'logo')->first();
            if ($oldSetting && $oldSetting->value) {
                Storage::disk('public')->delete($oldSetting->value);
            }

            Setting::updateOrCreate(['key' => 'logo'], ['value' => $path]);
        }

        // Handle hero image file upload
        if ($request->hasFile('hero_image')) {
            $request->validate([
                'hero_image' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image')->store('settings', 'public');

            // Hapus gambar hero lama jika ada
            $oldHero = Setting::where('key', 'hero_image')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image'], ['value' => $path]);
        }

        // Handle hero image 2 file upload
        if ($request->hasFile('hero_image_2')) {
            $request->validate([
                'hero_image_2' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image_2')->store('settings', 'public');

            // Hapus gambar hero lama jika ada
            $oldHero = Setting::where('key', 'hero_image_2')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image_2'], ['value' => $path]);
        }

        // Handle hero image 3 file upload
        if ($request->hasFile('hero_image_3')) {
            $request->validate([
                'hero_image_3' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image_3')->store('settings', 'public');

            // Hapus gambar hero lama jika ada
            $oldHero = Setting::where('key', 'hero_image_3')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image_3'], ['value' => $path]);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Konfigurasi Sistem berhasil diperbarui.');
    }
}
