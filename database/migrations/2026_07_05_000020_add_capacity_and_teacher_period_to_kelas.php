<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedInteger('kapasitas')->nullable()->after('tahun_ajaran');
            $table->unique(['wali_kelas_id', 'tahun_ajaran'], 'kelas_wali_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique('kelas_wali_tahun_unique');
            $table->dropColumn('kapasitas');
        });
    }
};
