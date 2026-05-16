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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action_type'); // e.g., 'Tambah', 'Update', 'Hapus'
            $table->string('module'); // e.g., 'Berita', 'Ekstrakurikuler'
            $table->text('description'); // e.g., 'Menambahkan berita baru: Judul Berita'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
