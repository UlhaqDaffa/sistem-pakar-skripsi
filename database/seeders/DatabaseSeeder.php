<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\KategoriPertanyaanSeeder;
use Database\Seeders\PertanyaanSeeder;
use Database\Seeders\MinatBidangSeeder;
use Database\Seeders\AreaRisetSeeder;
use Database\Seeders\PolaJudulSeeder;
use Database\Seeders\DecisionTreeRulesSeeder;

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
        
        $this->call(MinatBidangSeeder::class);
        $this->call(AreaRisetSeeder::class);
        $this->call(PolaJudulSeeder::class);

        // Seeder lainnya
        $this->call(MataKuliahKunciSeeder::class);
        $this->call(KonfigurasiPembobotanSeeder::class);
        $this->call(RulesSeeder::class);
        $this->call(DecisionTreeRulesSeeder::class);

        // Deprecated: MinatDanRisetSeeder sudah diganti dengan MinatBidangSeeder dan AreaRisetSeeder
        // $this->call(MinatDanRisetSeeder::class);

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
