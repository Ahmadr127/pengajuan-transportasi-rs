<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'driver.bambang'],
            [
                'first_name' => 'Bambang',
                'last_name' => 'Prasetyo',
                'username' => 'driver.bambang',
                'password' => Hash::make('rsazra'),
                'role' => 'driver',
                'priority_level' => 0,
            ]
        );

        Driver::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'name' => 'Bambang Prasetyo',
                'phone' => '081234567890',
                'license_number' => 'SIM-A-001',
                'is_active' => true,
            ]
        );
    }
}
