<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'test.admin'],
            [
                'first_name'    => 'Admin',
                'last_name'     => '',
                'username'      => 'admin',
                'password'      => Hash::make('rsazra'),
                'role'          => 'admin',
                'priority_level' => 0,
            ]
        );
    }
}
