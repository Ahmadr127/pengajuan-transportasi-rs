<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AclSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            DriverSeeder::class,
            SimutuOrganisasiSeeder::class,
        ]);
    }
}
