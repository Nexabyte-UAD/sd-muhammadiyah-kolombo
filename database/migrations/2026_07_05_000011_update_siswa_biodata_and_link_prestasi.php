<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('agama', 30)->nullable()->after('jenis_kelamin');
            $table->dropUnique('siswas_nisn_unique');
            $table->dropColumn('nisn');
        });

        Schema::table('prestasis', function (Blueprint $table) {
            $table->foreignId('siswa_id')
                ->nullable()
                ->after('kategori')
                ->constrained('siswas')
                ->nullOnDelete();
        });

        DB::table('prestasis')
            ->whereNotNull('nama_siswa')
            ->orderBy('id')
            ->eachById(function ($prestasi) {
                $siswaId = DB::table('siswas')
                    ->where('nama', $prestasi->nama_siswa)
                    ->value('id');

                if ($siswaId) {
                    DB::table('prestasis')
                        ->where('id', $prestasi->id)
                        ->update(['siswa_id' => $siswaId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('prestasis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('siswa_id');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('agama');
            $table->string('nisn')->nullable()->unique()->after('nis');
        });
    }
};
