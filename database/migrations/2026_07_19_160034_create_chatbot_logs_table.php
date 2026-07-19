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
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->text('question');
            $table->string('answer_source');
            $table->foreignId('matched_faq_id')->nullable()->constrained('chatbot_faqs')->nullOnDelete();
            $table->text('response_text')->nullable();
            $table->string('status')->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('answer_source');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
