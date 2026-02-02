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
        Schema::create('konfigurasi_pembobotan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_konfigurasi')->default('default');
            $table->decimal('bobot_minat', 3, 2)->default(0.60)->comment('Bobot untuk skor minat (0.00-1.00)');
            $table->decimal('bobot_asesmen', 3, 2)->default(0.40)->comment('Bobot untuk skor asesmen (0.00-1.00)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_pembobotan');
    }
};
