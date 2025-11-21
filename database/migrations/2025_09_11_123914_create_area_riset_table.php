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
        Schema::create('area_riset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_area')->unique();
            $table->string('nama_area');
            $table->text('deskripsi');
            $table->text('contoh_studi_kasus')->nullable();
            $table->text('tujuan_masalah')->nullable();
            $table->text('tipe_sistem')->nullable();
            $table->enum('target_arketipe', ['CREATOR', 'ANALIS', 'ARCHITECT', 'GENERAL'])->default('GENERAL');
            $table->unsignedTinyInteger('level_kesulitan')->default(2)->comment('1=Low, 2=Medium, 3=High');
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_riset');
    }
};


