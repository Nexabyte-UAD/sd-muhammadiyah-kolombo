<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('kelas')
            ->where('is_active', false)
            ->whereNull('wali_kelas_id')
            ->where('jurusan', 'Umum')
            ->delete();

        Schema::table('kelas', function (Blueprint $table) {
            $table->string('tingkat', 100)->change();
            $table->string('jurusan', 100)->nullable()->default(null)->change();
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('jurusan', 100)->default('Umum')->nullable(false)->change();
            $table->boolean('is_active')->default(true)->after('jurusan');
        });
    }
};
