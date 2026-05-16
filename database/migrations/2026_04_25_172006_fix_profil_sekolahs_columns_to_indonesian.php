<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_sekolahs', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('type');
        });
        
        // Rename using raw statements to avoid requiring doctrine/dbal
        DB::statement('ALTER TABLE profil_sekolahs CHANGE content konten TEXT NULL');
        DB::statement('ALTER TABLE profil_sekolahs CHANGE image gambar VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE profil_sekolahs CHANGE konten content TEXT NULL');
        DB::statement('ALTER TABLE profil_sekolahs CHANGE gambar image VARCHAR(255) NULL');
        
        Schema::table('profil_sekolahs', function (Blueprint $table) {
            $table->dropColumn('judul');
        });
    }
};
