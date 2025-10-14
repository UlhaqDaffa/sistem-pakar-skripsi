<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MataKuliahKunci;
use Illuminate\Support\Facades\DB;


class MatkulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        MataKuliahKunci::truncate();
        MataKuliahKunci::insert([
            ['nama_mata_kuliah' => 'Pemrograman'],
            ['nama_mata_kuliah' => 'Algoritma dan Struktur Data'],
            ['nama_mata_kuliah' => 'Jaringan Komputer'],
            ['nama_mata_kuliah' => 'Basis Data'],
            ['nama_mata_kuliah' => 'Kecerdasan Buatan'],
        ]);
    }
}
