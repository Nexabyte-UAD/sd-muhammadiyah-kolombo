<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\IndonesianTextFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $request->validate([
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            'hero_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            'hero_image_3' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
            'welcome_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'remove_hero_image_2' => ['nullable', 'boolean'],
            'remove_hero_image_3' => ['nullable', 'boolean'],
            'remove_welcome_image' => ['nullable', 'boolean'],
        ]);

        // Mendapatkan semua parameter input teks kecuali token, metode, dan berkas gambar
        $data = $request->except([
            '_token', '_method', 'logo',
            'hero_image', 'hero_image_2', 'hero_image_3', 'welcome_image',
            'remove_hero_image', 'remove_hero_image_2', 'remove_hero_image_3', 'remove_welcome_image',
        ]);
        
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

        foreach (['hero_image', 'hero_image_2', 'hero_image_3', 'welcome_image'] as $imageKey) {
            if ($request->hasFile($imageKey)) {
                $this->storeSettingImage($request, $imageKey);
            } elseif ($request->boolean('remove_'.$imageKey)) {
                $this->removeSettingImage($imageKey);
            }
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Konfigurasi Sistem berhasil diperbarui.');
    }

    private function storeSettingImage(Request $request, string $key): void
    {
        try {
            $path = $request->file($key)->store('settings', 'public');
        } catch (\Throwable $exception) {
            report($exception);
            $path = false;
        }

        if (!$path) {
            throw ValidationException::withMessages([
                $key => 'Gambar gagal disimpan. Pastikan folder storage di server dapat ditulis.',
            ]);
        }

        $oldPath = Setting::where('key', $key)->value('value');
        Setting::updateOrCreate(['key' => $key], ['value' => $path]);

        if ($oldPath && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    private function removeSettingImage(string $key): void
    {
        $setting = Setting::where('key', $key)->first();
        if (!$setting) {
            return;
        }

        if ($setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();
    }
}
