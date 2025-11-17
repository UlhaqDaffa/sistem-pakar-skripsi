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
        // === TABEL INTI PENGGUNA ===
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 1. Kategori Minat Luas
        Schema::create('minat_bidang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bidang')->unique()->comment('Contoh: WEB_DEV, AI_ML');
            $table->string('nama_bidang');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. Area Riset Spesifik
        Schema::create('area_riset', function (Blueprint $table) {
            $table->id();
            $table->string('kode_area')->unique()->comment('Contoh: SISTEM_REKOMENDASI');
            $table->string('nama_area');
            $table->text('deskripsi');
            $table->text('kata_kunci_teknologi')->comment('Contoh: Python, Scikit-learn, Pandas');
            $table->text('kata_kunci_metode')->comment('Contoh: Collaborative Filtering, KNN');
            $table->text('contoh_studi_kasus')->comment('Contoh: Rekomendasi Film, E-commerce');
            $table->text('tujuan_masalah')->comment('Deskripsi tujuan/masalah yang diselesaikan untuk formulasi judul');
            $table->text('tipe_sistem')->comment('Tipe sistem yang dikembangkan untuk formulasi judul');
            $table->timestamps();
        });


        // === TABEL UNTUK KUESIONER RULE-BASED ===

        // Kategori untuk mengelompokkan pertanyaan (Umum, Minat, Asesmen)
        Schema::create('kategori_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kategori')->unique();
            $table->string('nama_kategori');
            $table->enum('tipe', ['umum', 'minat', 'asesmen']);
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel utama untuk semua pertanyaan
        Schema::create('pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_pertanyaan')->cascadeOnDelete();
            $table->string('kode_pertanyaan')->unique();
            $table->text('teks_pertanyaan');
            $table->boolean('is_start_point')->default(false)->comment('Penanda pertanyaan paling pertama');
            $table->timestamps();
        });

        // Tabel untuk opsi jawaban dari pertanyaan
        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan')->cascadeOnDelete();
            $table->string('kode_jawaban');
            $table->string('teks_jawaban');
            $table->integer('nilai')->comment('Untuk skala Likert atau pembobotan lainnya');
            $table->timestamps();
        });


        // === TABEL PENCATATAN RIWAYAT KONSULTASI ===
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['selesai', 'berjalan'])->default('berjalan');
            $table->foreignId('hasil_minat_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->foreignId('hasil_akademik_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->foreignId('area_riset_final_id')->nullable()->constrained('area_riset')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('jawaban_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->cascadeOnDelete();
            $table->foreignId('opsi_jawaban_id')->constrained('opsi_jawaban')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_konsultasi');
        Schema::dropIfExists('konsultasi');
        Schema::dropIfExists('opsi_jawaban');
        Schema::dropIfExists('pertanyaan');
        Schema::dropIfExists('kategori_pertanyaan');
        Schema::dropIfExists('area_riset_minat_bidang');
        Schema::dropIfExists('area_riset');
        Schema::dropIfExists('minat_bidang');
        Schema::dropIfExists('users');
    }
};
