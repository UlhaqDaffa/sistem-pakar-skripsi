<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KonfigurasiPembobotan;
use Illuminate\Support\Facades\DB;

class KonfigurasiPembobotanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KonfigurasiPembobotan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat konfigurasi default
        KonfigurasiPembobotan::create([
            'nama_konfigurasi' => 'default',
            'bobot_minat' => 0.60,
            'bobot_asesmen' => 0.40,
            'is_active' => true,
        ]);
    }
}
