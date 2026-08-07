<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'user.ahmad'],
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Fauzi',
                'username' => 'user.ahmad',
                'password' => Hash::make('rsazra'),
                'role' => 'user',
                'priority_level' => 0,
            ]
        );
    }
}
