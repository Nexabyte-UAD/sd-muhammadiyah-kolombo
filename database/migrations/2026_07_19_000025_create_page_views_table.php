<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: create_page_views_table
 * 
 * Membuat tabel page_views untuk mencatat setiap kunjungan halaman publik website
 * SD Muhammadiyah Komplek Kolombo oleh pengunjung. Data ini digunakan oleh
 * Dashboard Analitik Admin untuk menampilkan statistik trafik website.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('page', 100)->comment('Path URL halaman yang dikunjungi, misal: /, /berita, /guru');
            $table->string('page_label', 100)->nullable()->comment('Nama tampilan halaman, misal: Beranda, Berita, Guru & Staf');
            $table->string('ip_address', 45)->nullable()->comment('Alamat IP pengunjung (IPv4/IPv6)');
            $table->string('user_agent', 300)->nullable()->comment('Browser/device pengunjung');
            $table->timestamp('visited_at')->useCurrent()->comment('Waktu kunjungan');

            // Index untuk mempercepat query agregasi per halaman dan per waktu
            $table->index('page');
            $table->index('visited_at');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
