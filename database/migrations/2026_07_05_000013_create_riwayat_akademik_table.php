<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('status', 20)->default('aktif')->change();
        });

        Schema::create('riwayat_akademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('tahun_ajaran', 20);
            $table->string('kelas_asal', 100)->nullable();
            $table->string('kelas_tujuan', 100)->nullable();
            $table->enum('keputusan', ['naik', 'tinggal', 'lulus', 'pindah']);
            $table->text('catatan')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tanggal_proses');
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_akademik');

        Schema::table('siswas', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'alumni'])->default('aktif')->change();
        });
    }
};
