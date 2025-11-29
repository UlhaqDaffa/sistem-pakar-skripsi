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
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['selesai', 'berjalan'])->default('berjalan');
            $table->foreignId('hasil_minat_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->foreignId('hasil_akademik_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->foreignId('area_riset_final_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};





