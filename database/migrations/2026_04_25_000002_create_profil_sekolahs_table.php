<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // sambutan, visi_misi, sejarah
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_sekolahs');
    }
};
