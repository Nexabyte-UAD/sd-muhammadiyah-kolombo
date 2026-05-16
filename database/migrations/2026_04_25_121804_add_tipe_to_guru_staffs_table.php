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
        Schema::table('guru_staffs', function (Blueprint $table) {
            $table->enum('tipe', ['guru', 'staf'])->default('guru')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_staffs', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
