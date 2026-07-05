<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->nullable()->after('tingkat');
            $table->string('tahun_ajaran', 20)->nullable()->after('urutan');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->after('kelas')
                ->constrained('kelas')->nullOnDelete();
        });

        DB::table('siswas')->whereNotNull('kelas')->orderBy('id')->eachById(function ($siswa) {
            $kelasId = DB::table('kelas')->where('tingkat', $siswa->kelas)->value('id');
            if ($kelasId) {
                DB::table('siswas')->where('id', $siswa->id)->update(['kelas_id' => $kelasId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswas', fn (Blueprint $table) => $table->dropConstrainedForeignId('kelas_id'));
        Schema::table('kelas', fn (Blueprint $table) => $table->dropColumn(['urutan', 'tahun_ajaran']));
    }
};
