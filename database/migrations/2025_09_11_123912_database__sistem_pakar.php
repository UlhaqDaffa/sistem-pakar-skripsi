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

        Schema::create('topik_penelitian', function (Blueprint $table) {
            $table->id('id_topik');
            $table->string('judul');
            $table->string('deskripsi');
            $table->string('kategori');
            $table->timestamps();
        });

        Schema::create('aturan_rbs', function (Blueprint $table) {
            $table->id('id_aturan');
            $table->string('kode_aturan')->unique();
            $table->string('nama_aturan');
            $table->timestamps();
        });

        Schema::create('kondisi_aturan', function (Blueprint $table) {
            $table->id('id_kondisi');
            $table->unsignedBigInteger('id_aturan');
            $table->foreign('id_aturan')
                ->references('id_aturan')
                ->on('aturan_rbs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('faktor');
            $table->string('operator');
            $table->string('nilai');
            $table->timestamps();
        });

        Schema::create('jenis_jawaban', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_jawaban_id')->constrained('jenis_jawaban')->cascadeOnDelete();
            $table->string('teks_jawaban');
            $table->integer('nilai')->nullable();
            $table->timestamps();
        });

        Schema::create('kategori_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_jawaban_id')->constrained('jenis_jawaban')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_pertanyaan')->cascadeOnDelete();
            $table->string('teks_pertanyaan');
            $table->enum('tipe_jawaban', ['pilihan_ganda', 'input_nilai']);
            $table->integer('urutan')->nullable();
            $table->timestamps();
        });


        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('kesimpulan')->nullable();
            $table->text('topik_rekomendasi')->nullable();
            $table->timestamps();
        });

        Schema::create('konsultasi_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi')->cascadeOnDelete();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan')->cascadeOnDelete();
            $table->foreignId('opsi_jawaban_id')->nullable()->constrained('opsi_jawaban')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi_detail');
        Schema::dropIfExists('konsultasi');
        Schema::dropIfExists('opsi_jawaban');
        Schema::dropIfExists('jenis_jawaban');
        Schema::dropIfExists('kategori_pertanyaan');
        Schema::dropIfExists('pertanyaan');
        Schema::dropIfExists('kondisi_aturan');
        Schema::dropIfExists('aturan_rbs');
        Schema::dropIfExists('topik_penelitian');
        Schema::dropIfExists('users');
    }
};
