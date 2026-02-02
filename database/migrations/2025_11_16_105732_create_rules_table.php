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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rule')->unique()->comment('Kode unik rule, contoh: RULE_RPL_01');
            $table->string('nama_rule')->comment('Nama rule untuk identifikasi');
            $table->text('deskripsi')->nullable()->comment('Deskripsi rule');

            // Aksi THEN (dalam format JSON), tetap dipertahankan untuk fleksibilitas skor_boost, dll.
            $table->json('aksi')->comment('Aksi THEN dalam format JSON');

            // Rentang skor total minat yang didukung rule ini
            $table->integer('min_score')->default(0)->comment('Batas bawah skor total minat');
            $table->integer('max_score')->default(100)->comment('Batas atas skor total minat');

            // Level skill minimum yang disyaratkan (1-4)
            $table->tinyInteger('min_skill_level')->default(1)->comment('Level skill minimum yang dibutuhkan');

            // Daftar arketipe yang diizinkan, contoh: ["CREATOR", "GENERAL"]
            $table->json('allowed_archetypes')->nullable()->comment('Arketipe pengguna yang diizinkan');

            // Jenis engine yang menggunakan rule ini: rule_based / decision_tree
            $table->string('engine_type')->default('rule_based')->comment('Tipe engine: rule_based atau decision_tree');

            // Konfigurasi khusus Decision Tree (pengganti kondisi JSON lama untuk DT)
            $table->json('dt_config')->nullable()->comment('Konfigurasi khusus untuk engine decision_tree');
            
            // Relasi ke minat_bidang dan area_riset (optional, untuk referensi)
            $table->foreignId('minat_bidang_id')->nullable()->constrained('minat_bidang')->nullOnDelete();
            $table->foreignId('area_riset_id')->nullable()->constrained('area_riset')->nullOnDelete();
            
            // Prioritas untuk urutan evaluasi (semakin kecil, semakin prioritas)
            $table->integer('prioritas')->default(100)->comment('Prioritas evaluasi (1 = tertinggi)');
            
            // Status aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['is_active', 'prioritas']);
            $table->index(['min_score', 'max_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
