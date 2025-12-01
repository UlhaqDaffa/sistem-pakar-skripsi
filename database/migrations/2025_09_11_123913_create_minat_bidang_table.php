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
        Schema::create('minat_bidang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bidang')->unique();
            $table->string('nama_bidang');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('minat_bidang');
    }
};








