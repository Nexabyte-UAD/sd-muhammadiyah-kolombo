<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $profilSingkat = DB::table('profil_sekolahs')
            ->where('type', 'profil_singkat')
            ->first();

        if (! $profilSingkat) {
            return;
        }

        $tentang = DB::table('profil_sekolahs')
            ->where('type', 'tentang')
            ->first();

        if ($tentang) {
            DB::table('profil_sekolahs')
                ->where('id', $tentang->id)
                ->update([
                    'judul' => filled($tentang->judul) ? $tentang->judul : $profilSingkat->judul,
                    'konten' => filled($tentang->konten) ? $tentang->konten : $profilSingkat->konten,
                    'gambar' => filled($tentang->gambar) ? $tentang->gambar : $profilSingkat->gambar,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('profil_sekolahs')->insert([
                'type' => 'tentang',
                'judul' => $profilSingkat->judul,
                'konten' => $profilSingkat->konten,
                'gambar' => $profilSingkat->gambar,
                'created_at' => $profilSingkat->created_at ?? now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('profil_sekolahs')
            ->where('type', 'profil_singkat')
            ->delete();
    }

    public function down(): void
    {
        if (DB::table('profil_sekolahs')->where('type', 'profil_singkat')->exists()) {
            return;
        }

        $tentang = DB::table('profil_sekolahs')
            ->where('type', 'tentang')
            ->first();

        if ($tentang) {
            DB::table('profil_sekolahs')->insert([
                'type' => 'profil_singkat',
                'judul' => $tentang->judul,
                'konten' => $tentang->konten,
                'gambar' => $tentang->gambar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
