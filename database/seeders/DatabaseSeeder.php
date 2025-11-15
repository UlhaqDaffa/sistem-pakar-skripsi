<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\KategoriPertanyaanSeeder;
use Database\Seeders\PertanyaanSeeder;

use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();

        $this->call(UsersSeeder::class);
        $this->call(KategoriPertanyaanSeeder::class);
        $this->call(PertanyaanSeeder::class);
        $this->call(MinatDanRisetSeeder::class);

        // $this->call(MatkulSeeder::class);

        // DB::table('mata_kuliah_kunci')->insert([
        //     ['nama_mata_kuliah' => 'Pemrograman'],
        //     ['nama_mata_kuliah' => 'Algoritma dan Struktur Data'],
        //     ['nama_mata_kuliah' => 'Jaringan Komputer'],
        //     ['nama_mata_kuliah' => 'Basis Data'],
        //     ['nama_mata_kuliah' => 'Kecerdasan Buatan'],
        // ]);

        // MataKuliahKunci::factory(1)->create(
        //     [
        //         'nama_mata_kuliah' => [ 'Pemrograman', 'Algoritma dan Struktur Data', 'Jaringan Komputer', 'Basis Data', 'Kecerdasan Buatan' ]
        //     ]
        // );

        // User::factory(10)->create();
    }
}
