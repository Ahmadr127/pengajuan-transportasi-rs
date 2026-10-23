<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'first_name'    => 'Admin',
                'last_name'     => '',
                'password'      => Hash::make('rsazra'),
                'role'          => 'super-admin',
                'priority_level' => 0,
            ]
        );

        // Akun admin utama harus bisa mengelola role & menu (RBAC).
        $admin->syncRolesByName(['super-admin']);
    }
}
