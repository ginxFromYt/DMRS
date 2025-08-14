<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first, then users with their roles, then events
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            EventSeeder::class,
        ]);
    }
}
