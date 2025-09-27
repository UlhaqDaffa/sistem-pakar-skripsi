<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\AdminUserSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test admin',
        //     'email' => 'test@example.com',
        //     'role' => 'admin',
        //     'password' => Hash::make('test1234'),
        // ]);
    }
}
