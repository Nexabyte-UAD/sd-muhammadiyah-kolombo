<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            $table->string('nama_siswa')->nullable()->after('kategori');
            $table->string('prestasi_medali')->nullable()->after('nama_siswa');
            $table->string('penyelenggara')->nullable()->after('prestasi_medali');
        });
    }

    public function down(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropColumn(['nama_siswa', 'prestasi_medali', 'penyelenggara']);
        });
    }
};
