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
        Schema::create('guru_menulis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('penulis'); // Nama Guru / Staf Penulis
            $table->foreignId('guru_staff_id')->nullable()->constrained('guru_staffs')->nullOnDelete();
            $table->string('kategori')->default('Opini & Artikel');
            $table->string('gambar')->nullable();
            $table->longText('isi');
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_menulis');
    }
};
