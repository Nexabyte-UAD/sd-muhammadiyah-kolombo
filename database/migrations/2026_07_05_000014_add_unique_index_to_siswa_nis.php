<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('siswas')
            ->whereNotNull('nis')
            ->where('nis', '!=', '')
            ->select('nis')
            ->groupBy('nis')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nis')
            ->each(function ($nis) {
                $pemilikUtama = DB::table('siswas')
                    ->where('nis', $nis)
                    ->min('id');

                DB::table('siswas')
                    ->where('nis', $nis)
                    ->where('id', '!=', $pemilikUtama)
                    ->update(['nis' => null]);
            });

        Schema::table('siswas', function (Blueprint $table) {
            $table->unique('nis');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropUnique('siswas_nis_unique');
        });
    }
};
