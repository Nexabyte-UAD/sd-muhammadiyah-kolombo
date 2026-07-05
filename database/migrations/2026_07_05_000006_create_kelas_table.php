<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->enum('tingkat', ['1', '2', '3', '4', '5', '6'])->unique();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('guru_staffs')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('kelas')->insert(
            collect(range(1, 6))->map(fn ($tingkat) => [
                'tingkat' => (string) $tingkat,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
