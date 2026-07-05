<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_sekolahs', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('type');
            $table->renameColumn('content', 'konten');
            $table->renameColumn('image', 'gambar');
        });
    }

    public function down(): void
    {
        Schema::table('profil_sekolahs', function (Blueprint $table) {
            $table->renameColumn('konten', 'content');
            $table->renameColumn('gambar', 'image');
            $table->dropColumn('judul');
        });
    }
};
