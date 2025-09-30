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

        Schema::create('riwayat_rekomendasi', function (Blueprint $table) {
            $table->id('id_riwayatrekom');
            $table->foreignId('id_user')
            ->constrained('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('riwayat_input', function (Blueprint $table) {
            $table->id('id_riwayatinput');
            $table->unsignedBigInteger('id_riwayatrekom');
            $table->foreign('id_riwayatrekom')
             ->references('id_riwayatrekom')
             ->on('riwayat_rekomendasi')
             ->onDelete('cascade')
             ->onUpdate('cascade');
            $table->string('faktor');
            $table->string('nilai');
            $table->timestamps();
        });

        Schema::create('riwayat_hasil', function (Blueprint $table) {
            $table->id('id_riwayathasil');
            $table->unsignedBigInteger('id_riwayatrekom');
            $table->foreign('id_riwayatrekom')
             ->references('id_riwayatrekom')
             ->on('riwayat_rekomendasi')
             ->onDelete('cascade')
             ->onUpdate('cascade');
            $table->unsignedBigInteger('id_topik');
            $table->foreign('id_topik')
             ->references('id_topik')
             ->on('topik_penelitian')
             ->onDelete('cascade')
             ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('pertanyaan', function (Blueprint $table) {


        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('topik_penelitian');
        Schema::dropIfExists('aturan_rbs');
        Schema::dropIfExists('kondisi_aturan');
        Schema::dropIfExists('riwayat_rekomendasi');
        Schema::dropIfExists('riwayat_input');
        Schema::dropIfExists('riwayat_hasil');
    }
};
