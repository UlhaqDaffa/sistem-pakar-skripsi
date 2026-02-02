<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataKuliahKunci;
use Illuminate\Support\Facades\DB;

class MataKuliahKunciSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MataKuliahKunci::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        MataKuliahKunci::create([
            'kode_mata_kuliah' => 'ALGORITMA',
            'nama_mata_kuliah' => 'Algoritma & Struktur Data',
        ]);

        MataKuliahKunci::create([
            'kode_mata_kuliah' => 'PEMROGRAMAN',
            'nama_mata_kuliah' => 'Pemrograman',
        ]);

        MataKuliahKunci::create([
            'kode_mata_kuliah' => 'BASIS_DATA',
            'nama_mata_kuliah' => 'Basis Data',
        ]);

        MataKuliahKunci::create([
            'kode_mata_kuliah' => 'KECERDASAN_BUATAN',
            'nama_mata_kuliah' => 'Kecerdasan Buatan',
        ]);
    }
}
