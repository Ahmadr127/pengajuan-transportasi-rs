<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menyiapkan menu yang dipakai navigasi mobile (bottom nav) dan
 * mengelompokkan menu master data di bawah satu menu induk "Master".
 *
 * Idempoten: memakai updateOrInsert sehingga aman dijalankan pada database
 * yang menunya sudah pernah diubah manual lewat UI.
 */
return new class extends Migration
{
    /** @var array<int, string> */
    private array $masterChildren = [
        'admin.users',
        'admin.units',
        'admin.jabatan',
        'admin.vehicles',
        'admin.drivers',
        'admin.recurring',
    ];

    public function up(): void
    {
        $now = now();

        $this->upsertMenu([
            'key' => 'admin.master',
            'context' => 'admin',
            'section' => null,
            'label' => 'Master',
            'route_name' => null,
            'url' => null,
            'icon' => 'storage',
            'permission_name' => null,
            'sort_order' => 40,
            'is_active' => true,
            'is_landing' => false,
            'show_sidebar' => false,
            'show_bottom_nav' => true,
            'active_pattern' => null,
        ], $now);

        $this->upsertMenu([
            'key' => 'admin.profile',
            'context' => 'admin',
            'section' => null,
            'label' => 'Profil',
            'route_name' => 'profil',
            'url' => null,
            'icon' => 'user',
            'permission_name' => 'profile.view',
            'sort_order' => 100,
            'is_active' => true,
            'is_landing' => false,
            'show_sidebar' => false,
            'show_bottom_nav' => true,
            'active_pattern' => 'profil',
        ], $now);

        $this->upsertMenu([
            'key' => 'user.dashboard',
            'context' => 'user',
            'section' => null,
            'label' => 'Dashboard',
            'route_name' => 'dashboard',
            'url' => null,
            'icon' => 'dashboard',
            'permission_name' => 'transport.request.create',
            'sort_order' => 5,
            'is_active' => true,
            'is_landing' => false,
            'show_sidebar' => false,
            'show_bottom_nav' => true,
            'active_pattern' => 'dashboard',
        ], $now);

        $this->upsertMenu([
            'key' => 'driver.dashboard',
            'context' => 'driver',
            'section' => null,
            'label' => 'Dashboard',
            'route_name' => 'driver.dashboard',
            'url' => null,
            'icon' => 'dashboard',
            'permission_name' => 'driver.task.view',
            'sort_order' => 5,
            'is_active' => true,
            'is_landing' => false,
            'show_sidebar' => false,
            'show_bottom_nav' => true,
            'active_pattern' => 'driver.dashboard',
        ], $now);

        $masterId = DB::table('menus')->where('key', 'admin.master')->value('id');

        if ($masterId) {
            DB::table('menus')
                ->whereIn('key', $this->masterChildren)
                ->update(['parent_id' => $masterId, 'updated_at' => $now]);
        }

        // Menu utama admin perlu tampil di bottom nav mobile.
        DB::table('menus')
            ->whereIn('key', ['admin.dashboard', 'admin.transport', 'admin.report'])
            ->update(['show_bottom_nav' => true, 'updated_at' => $now]);

        // Admin perlu bisa membuka profilnya sendiri untuk tab "Profil" di mobile.
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $profilePermissionId = DB::table('permissions')->where('name', 'profile.view')->value('id');

        if ($adminRoleId && $profilePermissionId) {
            DB::table('permission_role')->insertOrIgnore([
                'role_id' => $adminRoleId,
                'permission_id' => $profilePermissionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('menus')
            ->whereIn('key', $this->masterChildren)
            ->update(['parent_id' => null]);

        DB::table('menus')
            ->whereIn('key', ['admin.dashboard', 'admin.transport', 'admin.report'])
            ->update(['show_bottom_nav' => false]);

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $profilePermissionId = DB::table('permissions')->where('name', 'profile.view')->value('id');

        if ($adminRoleId && $profilePermissionId) {
            DB::table('permission_role')
                ->where('role_id', $adminRoleId)
                ->where('permission_id', $profilePermissionId)
                ->delete();
        }

        DB::table('menus')->whereIn('key', [
            'admin.master',
            'admin.profile',
            'user.dashboard',
            'driver.dashboard',
        ])->delete();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function upsertMenu(array $attributes, \Illuminate\Support\Carbon $now): void
    {
        DB::table('menus')->updateOrInsert(
            ['key' => $attributes['key']],
            $attributes + ['created_at' => $now, 'updated_at' => $now]
        );
    }
};
