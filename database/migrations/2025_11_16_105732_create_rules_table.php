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
            
            // Kondisi IF (dalam format JSON untuk fleksibilitas)
            // Contoh: {"conditions": [{"pertanyaan": "MINAT_CREATOR_01", "jawaban": "MINAT_WEB", "operator": "=="}, {"pertanyaan": "ASESMEN_WEB_01", "nilai": 4, "operator": ">="}]}
            $table->json('kondisi')->comment('Kondisi IF dalam format JSON');
            
            // Aksi THEN (dalam format JSON)
            // Contoh: {"minat_bidang": "RPL", "area_riset": "PENG-01", "skor_boost": 10}
            $table->json('aksi')->comment('Aksi THEN dalam format JSON');
            
            // Relasi ke minat_bidang dan area_riset (optional, untuk referensi)
            $table->foreignId('minat_bidang_id')->nullable()->constrained('minat_bidang')->nullOnDelete();
            $table->foreignId('area_riset_id')->nullable()->constrained('area_riset')->nullOnDelete();
            
            // Prioritas untuk urutan evaluasi (semakin kecil, semakin prioritas)
            $table->integer('prioritas')->default(100)->comment('Prioritas evaluasi (1 = tertinggi)');
            
            // Status aktif
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->index(['is_active', 'prioritas']);
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
