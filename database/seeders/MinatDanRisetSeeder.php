<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MinatDanRisetSeeder extends Seeder
{
    /**
     * Seeder lama digantikan oleh kombinasi MinatBidangSeeder dan AreaRisetSeeder.
     */
    public function run(): void
    {
        $this->call([
            MinatBidangSeeder::class,
            AreaRisetSeeder::class,
        ]);
    }
}
