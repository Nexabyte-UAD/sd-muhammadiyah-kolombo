<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_staffs', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['laki_laki', 'perempuan'])->nullable()->after('nama');
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'Honorer', 'GTY/GTT'])->nullable()->after('nip');
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'S1', 'S2', 'S3'])->nullable()->after('status_kepegawaian');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable()->after('pendidikan_terakhir');
        });
    }

    public function down(): void
    {
        Schema::table('guru_staffs', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'status_kepegawaian',
                'pendidikan_terakhir',
                'agama',
            ]);
        });
    }
};
