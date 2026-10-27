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
        $user = User::updateOrCreate(
            ['username' => 'user'],
            [
                'first_name' => 'User',
                'last_name' => 'User',
                'username' => 'user',
                'password' => Hash::make('rsazra'),
                'priority_level' => 0,
            ]
        );

        $user->syncRolesByName(['user']);
    }
}
