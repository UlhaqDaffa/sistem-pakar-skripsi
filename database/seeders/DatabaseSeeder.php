<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\AdminUserSeeder;
// use Database\Seeders\MatkulSeeder;
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

        $this->call(AdminUserSeeder::class);
        $this->call(KuesionerSeeder::class);

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

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('test1234'),
        ]);

    }
}
