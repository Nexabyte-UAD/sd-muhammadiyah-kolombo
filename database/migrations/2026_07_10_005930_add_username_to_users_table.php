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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
        });

        // Backfill existing users with default username (email prefix)
        foreach (Illuminate\Support\Facades\DB::table('users')->get() as $user) {
            $username = explode('@', $user->email)[0];
            
            // Handle uniqueness just in case
            $baseUsername = $username;
            $counter = 1;
            while (Illuminate\Support\Facades\DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
