<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('kategori_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori', 32)->unique();
            $table->string('nama_kategori', 32);
            $table->enum('tipe', ['umum', 'minat', 'asesmen', 'discriminator']);
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pertanyaan');
    }
};








