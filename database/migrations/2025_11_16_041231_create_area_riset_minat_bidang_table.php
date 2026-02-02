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
        Schema::create('area_riset_minat_bidang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_riset_id')->constrained('area_riset')->cascadeOnDelete();
            $table->foreignId('minat_bidang_id')->constrained('minat_bidang')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['area_riset_id', 'minat_bidang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_riset_minat_bidang');
    }
};
