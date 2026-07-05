<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('melanjutkan_sekolah_di')->nullable()->after('tahun_lulus');
            $table->string('jenjang')->nullable()->after('melanjutkan_sekolah_di');
            $table->string('program_studi')->nullable()->after('jenjang');
            $table->string('pekerjaan')->nullable()->after('program_studi');
            $table->string('bekerja_di_perusahaan')->nullable()->after('pekerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'melanjutkan_sekolah_di',
                'jenjang',
                'program_studi',
                'pekerjaan',
                'bekerja_di_perusahaan',
            ]);
        });
    }
};
