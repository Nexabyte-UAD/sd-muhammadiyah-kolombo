<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pendidikan_alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('jenjang', 50);
            $table->string('institusi');
            $table->string('jurusan')->nullable();
            $table->unsignedSmallInteger('tahun_masuk')->nullable();
            $table->unsignedSmallInteger('tahun_selesai')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_pekerjaan_alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('pekerjaan');
            $table->string('perusahaan')->nullable();
            $table->unsignedSmallInteger('tahun_mulai')->nullable();
            $table->unsignedSmallInteger('tahun_selesai')->nullable();
            $table->timestamps();
        });

        DB::table('siswas')->orderBy('id')->eachById(function ($siswa) {
            if ($siswa->melanjutkan_sekolah_di || $siswa->jenjang || $siswa->program_studi) {
                DB::table('riwayat_pendidikan_alumni')->insert([
                    'siswa_id' => $siswa->id,
                    'jenjang' => $siswa->jenjang ?: 'Lainnya',
                    'institusi' => $siswa->melanjutkan_sekolah_di ?: '-',
                    'jurusan' => $siswa->program_studi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($siswa->pekerjaan || $siswa->bekerja_di_perusahaan) {
                DB::table('riwayat_pekerjaan_alumni')->insert([
                    'siswa_id' => $siswa->id,
                    'pekerjaan' => $siswa->pekerjaan ?: '-',
                    'perusahaan' => $siswa->bekerja_di_perusahaan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'melanjutkan_sekolah_di', 'jenjang', 'program_studi',
                'pekerjaan', 'bekerja_di_perusahaan',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('melanjutkan_sekolah_di')->nullable();
            $table->string('jenjang')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('bekerja_di_perusahaan')->nullable();
        });

        Schema::dropIfExists('riwayat_pekerjaan_alumni');
        Schema::dropIfExists('riwayat_pendidikan_alumni');
    }
};
