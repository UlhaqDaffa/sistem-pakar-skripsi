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
        // Tabel template opsi jawaban
        Schema::create('opsi_jawaban_template', function (Blueprint $table) {
            $table->id();
            $table->string('kode_template')->unique()->comment('Kode unik template, contoh: LIKERT_1_5, ARKETIPE_UMUM');
            $table->string('nama_template')->comment('Nama template, contoh: Skala Likert 1-5');
            $table->text('deskripsi')->nullable()->comment('Deskripsi template');
            $table->timestamps();
        });

        // Tabel item-item dalam template
        Schema::create('opsi_jawaban_template_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('opsi_jawaban_template')->cascadeOnDelete();
            $table->string('kode_jawaban')->comment('Kode jawaban, contoh: SKALA_1, ARKETIPE_CREATOR');
            $table->string('teks_jawaban')->comment('Teks jawaban yang ditampilkan');
            $table->integer('nilai')->comment('Nilai untuk skala Likert atau pembobotan');
            $table->integer('urutan')->default(0)->comment('Urutan tampilan opsi jawaban');
            $table->timestamps();

            $table->index(['template_id', 'urutan']);
        });

        // Tabel pivot untuk relasi many-to-many antara pertanyaan dan template
        Schema::create('pertanyaan_opsi_jawaban_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertanyaan_id')->constrained('pertanyaan')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('opsi_jawaban_template')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('pertanyaan_id'); // Satu pertanyaan hanya bisa punya satu template
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_opsi_jawaban_template');
        Schema::dropIfExists('opsi_jawaban_template_item');
        Schema::dropIfExists('opsi_jawaban_template');
    }
};
