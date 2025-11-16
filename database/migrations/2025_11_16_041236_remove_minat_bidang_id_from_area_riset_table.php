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
        Schema::table('area_riset', function (Blueprint $table) {
            $table->dropForeign(['minat_bidang_id']);
            $table->dropColumn('minat_bidang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_riset', function (Blueprint $table) {
            $table->foreignId('minat_bidang_id')->after('kode_area')->constrained('minat_bidang')->cascadeOnDelete();
        });
    }
};
