<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller SettingController
 * 
 * Mengelola seluruh konfigurasi umum situs web (Settings),
 * seperti nama sekolah, nomor telepon, alamat, media sosial, serta tiga gambar hero banner utama.
 */
class SettingController extends Controller
{
    /**
     * Menampilkan halaman konfigurasi/pengaturan website sekolah.
     * Mengambil seluruh data key-value setting lalu mengubahnya ke bentuk associative array.
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Memperbarui seluruh konfigurasi situs web dan melakukan penanganan upload berkas banner hero slider.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IndonesianTextFormatter  $formatter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, IndonesianTextFormatter $formatter)
    {
        // Mendapatkan semua parameter input teks kecuali token, metode, dan berkas gambar
        $data = $request->except(['_token', '_method', 'logo', 'hero_image', 'hero_image_2', 'hero_image_3', 'welcome_image']); 
        
        // Bersihkan format input teks
        $data = $formatter->fields($data, [
            'nama_sekolah' => 'title',
            'beranda_profil_judul' => 'title',
            'kepsek_nama' => 'name',
            'beranda_profil_teks' => 'sentence',
            'kepsek_sambutan' => 'sentence',
            'telepon' => 'phone',
            'alamat' => 'address',
        ]);

        // Perbarui atau buat baru baris setting di database secara iteratif
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Jalankan upload file Gambar Hero Banner Utama ke-1 jika ada
        if ($request->hasFile('hero_image')) {
            $request->validate([
                'hero_image' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image')->store('settings', 'public');

            // Hapus gambar hero lama di disk
            $oldHero = Setting::where('key', 'hero_image')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image'], ['value' => $path]);
        }

        // Jalankan upload file Gambar Hero Banner Utama ke-2 jika ada
        if ($request->hasFile('hero_image_2')) {
            $request->validate([
                'hero_image_2' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image_2')->store('settings', 'public');

            // Hapus gambar hero lama di disk
            $oldHero = Setting::where('key', 'hero_image_2')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image_2'], ['value' => $path]);
        }

        // Jalankan upload file Gambar Hero Banner Utama ke-3 jika ada
        if ($request->hasFile('hero_image_3')) {
            $request->validate([
                'hero_image_3' => 'image|mimes:jpeg,png,jpg|max:4096',
            ]);

            $path = $request->file('hero_image_3')->store('settings', 'public');

            // Hapus gambar hero lama di disk
            $oldHero = Setting::where('key', 'hero_image_3')->first();
            if ($oldHero && $oldHero->value) {
                Storage::disk('public')->delete($oldHero->value);
            }

            Setting::updateOrCreate(['key' => 'hero_image_3'], ['value' => $path]);
        }

        // Jalankan upload file Gambar Selamat Datang jika ada
        if ($request->hasFile('welcome_image')) {
            $request->validate([
                'welcome_image' => 'image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            $path = $request->file('welcome_image')->store('settings', 'public');

            // Hapus gambar lama di disk
            $oldImage = Setting::where('key', 'welcome_image')->first();
            if ($oldImage && $oldImage->value) {
                Storage::disk('public')->delete($oldImage->value);
            }

            Setting::updateOrCreate(['key' => 'welcome_image'], ['value' => $path]);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Konfigurasi Sistem berhasil diperbarui.');
    }
}
