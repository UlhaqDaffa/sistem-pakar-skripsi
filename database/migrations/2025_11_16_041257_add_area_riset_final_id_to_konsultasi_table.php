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
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->foreignId('area_riset_final_id')->nullable()->after('hasil_akademik_id')->constrained('area_riset')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropForeign(['area_riset_final_id']);
            $table->dropColumn('area_riset_final_id');
        });
    }
};
