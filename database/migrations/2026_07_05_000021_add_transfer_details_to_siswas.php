<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->date('tanggal_keluar')->nullable()->after('status');
            $table->string('sekolah_tujuan')->nullable()->after('tanggal_keluar');
            $table->text('alasan_keluar')->nullable()->after('sekolah_tujuan');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', fn (Blueprint $table) => $table->dropColumn([
            'tanggal_keluar', 'sekolah_tujuan', 'alasan_keluar',
        ]));
    }
};
