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
        Schema::create('area_riset_pola_judul', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_riset_id')->constrained('area_riset')->cascadeOnDelete();
            $table->foreignId('pola_judul_id')->constrained('pola_judul')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['area_riset_id', 'pola_judul_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_riset_pola_judul');
    }
};
