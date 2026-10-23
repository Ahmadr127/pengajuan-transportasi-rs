<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AclSeeder extends Seeder
{
    /**
     * Katalog permission. Tambah di sini hanya jika ada fitur baru;
     * untuk kebutuhan harian, role & permission dikelola dari UI.
     */
    public const PERMISSIONS = [
        // group => [name => [label, description]]
        'Dashboard' => [
            'dashboard.admin.view' => ['Lihat dashboard admin', 'Akses halaman dashboard admin.'],
            'profile.view' => ['Lihat profil', 'Akses halaman profil sendiri.'],
            'notification.admin.view' => ['Notifikasi admin', 'Melihat bell notifikasi pengajuan masuk.'],
            'notification.user.view' => ['Notifikasi user', 'Melihat bell notifikasi status pengajuan.'],
        ],
        'Pengajuan' => [
            'transport.request.view' => ['Lihat pengajuan', 'Akses daftar & detail pengajuan admin.'],
            'transport.request.create' => ['Buat pengajuan', 'Akses form pengajuan umum & ambulance.'],
            'transport.request.history' => ['Riwayat pengajuan', 'Akses riwayat pengajuan sendiri.'],
            'transport.request.print' => ['Cetak pengajuan', 'Cetak surat pengajuan.'],
            'transport.request.approve' => ['Setujui pengajuan', 'Tombol setujui pengajuan.'],
            'transport.request.reject' => ['Tolak pengajuan', 'Tombol tolak pengajuan.'],
            'transport.request.assign' => ['Assign unit & driver', 'Tombol assign kendaraan dan driver.'],
            'transport.request.complete' => ['Selesaikan pengajuan', 'Tombol selesaikan perjalanan.'],
            'transport.request.update' => ['Update pengajuan', 'Tombol simpan perubahan pengajuan.'],
        ],
        'Laporan' => [
            'report.view' => ['Lihat laporan', 'Akses halaman laporan.'],
            'report.export' => ['Export laporan', 'Export laporan ke Excel.'],
            'report.print' => ['Cetak laporan', 'Cetak laporan.'],
        ],
        'Master Data' => [
            'user.manage' => ['Kelola user', 'Akses menu User / Akun.'],
            'user.create' => ['Tambah user', 'Tombol tambah user.'],
            'user.edit' => ['Edit user', 'Tombol edit user.'],
            'user.delete' => ['Hapus user', 'Tombol hapus user.'],
            'user.sync' => ['Sync Simutu', 'Tombol sinkronisasi user dari Simutu.'],
            'vehicle.manage' => ['Kelola kendaraan', 'Akses menu Kendaraan.'],
            'vehicle.create' => ['Tambah kendaraan', 'Tombol tambah kendaraan.'],
            'vehicle.edit' => ['Edit kendaraan', 'Tombol edit kendaraan.'],
            'vehicle.delete' => ['Hapus kendaraan', 'Tombol hapus kendaraan.'],
            'driver.manage' => ['Kelola driver', 'Akses menu Driver.'],
            'driver.create' => ['Tambah driver', 'Tombol tambah driver.'],
            'driver.edit' => ['Edit driver', 'Tombol edit driver.'],
            'driver.delete' => ['Hapus driver', 'Tombol hapus driver.'],
            'recurring.manage' => ['Kelola pengajuan berulang', 'Akses menu Pengajuan Berulang.'],
            'recurring.create' => ['Tambah template berulang', 'Tombol tambah template.'],
            'recurring.edit' => ['Edit template berulang', 'Tombol edit template.'],
            'recurring.delete' => ['Hapus template berulang', 'Tombol hapus template.'],
        ],
        'Driver' => [
            'driver.task.view' => ['Lihat tugas driver', 'Akses dashboard tugas driver.'],
            'driver.task.history' => ['Riwayat tugas driver', 'Akses riwayat tugas driver.'],
            'driver.task.complete' => ['Selesaikan tugas', 'Tombol selesaikan tugas.'],
            'driver.task.cancel' => ['Batalkan tugas', 'Tombol batalkan tugas.'],
            'driver.task.print' => ['Cetak tugas', 'Tombol cetak surat tugas.'],
        ],
        'Tanda Tangan' => [
            'signature.sign' => ['Tanda tangan', 'Akses halaman tanda tangan digital.'],
        ],
        'Pengaturan' => [
            'role.manage' => ['Kelola role & permission', 'Akses menu Role & Permission.'],
            'menu.manage' => ['Kelola menu', 'Akses menu Menu.'],
        ],
    ];

    public const ROLE_PERMISSIONS = [
        'super-admin' => '*',
        'admin' => [
            'dashboard.admin.view',
            'profile.view',
            'notification.admin.view',
            'transport.request.view',
            'transport.request.print',
            'transport.request.approve',
            'transport.request.reject',
            'transport.request.assign',
            'transport.request.complete',
            'transport.request.update',
            'report.view',
            'report.export',
            'report.print',
            'user.manage',
            'user.create',
            'user.edit',
            'user.delete',
            'user.sync',
            'vehicle.manage',
            'vehicle.create',
            'vehicle.edit',
            'vehicle.delete',
            'driver.manage',
            'driver.create',
            'driver.edit',
            'driver.delete',
            'recurring.manage',
            'recurring.create',
            'recurring.edit',
            'recurring.delete',
            'signature.sign',
        ],
        'user' => [
            'profile.view',
            'notification.user.view',
            'transport.request.create',
            'transport.request.history',
            'transport.request.print',
            'signature.sign',
        ],
        'driver' => [
            'profile.view',
            'driver.task.view',
            'driver.task.history',
            'driver.task.complete',
            'driver.task.cancel',
            'driver.task.print',
            'signature.sign',
        ],
    ];

    public function run(): void
    {
        $permissionIds = [];

        foreach (self::PERMISSIONS as $group => $permissions) {
            foreach ($permissions as $name => [$label, $description]) {
                $permission = Permission::updateOrCreate(
                    ['name' => $name],
                    ['group' => $group, 'label' => $label, 'description' => $description]
                );
                $permissionIds[$name] = $permission->id;
            }
        }

        $roles = [
            'super-admin' => ['label' => 'Super Admin', 'description' => 'Akses penuh termasuk pengaturan role & menu.', 'is_locked' => true, 'is_super' => true],
            'admin' => ['label' => 'Admin', 'description' => 'Pengelola transportasi.', 'is_locked' => true, 'is_super' => false],
            'driver' => ['label' => 'Driver', 'description' => 'Pengemudi.', 'is_locked' => true, 'is_super' => false],
            'user' => ['label' => 'User', 'description' => 'Pemohon transportasi.', 'is_locked' => true, 'is_super' => false],
        ];

        foreach ($roles as $name => $attributes) {
            $role = Role::updateOrCreate(['name' => $name], $attributes);

            $names = self::ROLE_PERMISSIONS[$name] ?? [];
            $ids = $names === '*' ? array_values($permissionIds) : array_map(
                fn ($permission) => $permissionIds[$permission] ?? null,
                $names
            );

            $role->permissions()->sync(array_filter($ids));
        }

        $this->seedMenus();

        // Pastikan user lama yang belum punya role tetap mendapat role.
        User::query()->with('roles')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                if ($user->roles->isEmpty()) {
                    $role = Role::where('name', $user->role)->first() ?? Role::where('name', 'user')->first();
                    if ($role) {
                        $user->roles()->syncWithoutDetaching([$role->id]);
                    }
                }
            }
        });
    }

    private function seedMenus(): void
    {
        $menus = [
            // Admin
            ['key' => 'admin.dashboard', 'context' => 'admin', 'section' => null, 'label' => 'Dashboard', 'route_name' => 'admin.dashboard', 'icon' => 'dashboard', 'permission_name' => 'dashboard.admin.view', 'sort_order' => 10, 'is_landing' => true, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.dashboard'],
            ['key' => 'admin.transport', 'context' => 'admin', 'section' => null, 'label' => 'Daftar Pengajuan', 'route_name' => 'admin.transport.index', 'icon' => 'document', 'permission_name' => 'transport.request.view', 'sort_order' => 20, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.transport.*'],
            ['key' => 'admin.report', 'context' => 'admin', 'section' => null, 'label' => 'Laporan', 'route_name' => 'admin.laporan', 'icon' => 'chart', 'permission_name' => 'report.view', 'sort_order' => 30, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.laporan*'],
            ['key' => 'admin.users', 'context' => 'admin', 'section' => 'Master Data', 'label' => 'User / Akun', 'route_name' => 'admin.users.index', 'icon' => 'users', 'permission_name' => 'user.manage', 'sort_order' => 40, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.users.*'],
            ['key' => 'admin.vehicles', 'context' => 'admin', 'section' => 'Master Data', 'label' => 'Kendaraan', 'route_name' => 'admin.vehicles.index', 'icon' => 'car', 'permission_name' => 'vehicle.manage', 'sort_order' => 50, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.vehicles.*'],
            ['key' => 'admin.drivers', 'context' => 'admin', 'section' => 'Master Data', 'label' => 'Driver', 'route_name' => 'admin.drivers.index', 'icon' => 'driver', 'permission_name' => 'driver.manage', 'sort_order' => 60, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.drivers.*'],
            ['key' => 'admin.recurring', 'context' => 'admin', 'section' => 'Master Data', 'label' => 'Pengajuan Berulang', 'route_name' => 'admin.recurring-templates.index', 'icon' => 'repeat', 'permission_name' => 'recurring.manage', 'sort_order' => 70, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.recurring-templates.*'],
            ['key' => 'admin.roles', 'context' => 'admin', 'section' => 'Pengaturan', 'label' => 'Role & Permission', 'route_name' => 'admin.roles.index', 'icon' => 'shield', 'permission_name' => 'role.manage', 'sort_order' => 80, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.roles.*'],
            ['key' => 'admin.menus', 'context' => 'admin', 'section' => 'Pengaturan', 'label' => 'Menu', 'route_name' => 'admin.menus.index', 'icon' => 'gear', 'permission_name' => 'menu.manage', 'sort_order' => 90, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => false, 'active_pattern' => 'admin.menus.*'],

            // User
            ['key' => 'user.transport.create', 'context' => 'user', 'section' => null, 'label' => 'Pengajuan', 'route_name' => 'dashboard', 'icon' => 'home', 'permission_name' => 'transport.request.create', 'sort_order' => 10, 'is_landing' => true, 'show_sidebar' => true, 'show_bottom_nav' => true, 'active_pattern' => 'home,pengajuan.choose,pengajuan.umum*,pengajuan.ambulance*'],
            ['key' => 'user.transport.history', 'context' => 'user', 'section' => null, 'label' => 'Riwayat', 'route_name' => 'pengajuan.index', 'icon' => 'history', 'permission_name' => 'transport.request.history', 'sort_order' => 20, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => true, 'active_pattern' => 'pengajuan.index,pengajuan.success,pengajuan.print'],
            ['key' => 'user.profile', 'context' => 'user', 'section' => null, 'label' => 'Profil', 'route_name' => 'profil', 'icon' => 'user', 'permission_name' => 'profile.view', 'sort_order' => 30, 'is_landing' => false, 'show_sidebar' => false, 'show_bottom_nav' => true, 'active_pattern' => 'profil'],

            // Driver
            ['key' => 'driver.task', 'context' => 'driver', 'section' => null, 'label' => 'Tugas Saya', 'route_name' => 'driver.dashboard', 'icon' => 'home', 'permission_name' => 'driver.task.view', 'sort_order' => 10, 'is_landing' => true, 'show_sidebar' => true, 'show_bottom_nav' => true, 'active_pattern' => 'driver.dashboard'],
            ['key' => 'driver.history', 'context' => 'driver', 'section' => null, 'label' => 'Riwayat', 'route_name' => 'driver.history', 'icon' => 'history', 'permission_name' => 'driver.task.history', 'sort_order' => 20, 'is_landing' => false, 'show_sidebar' => true, 'show_bottom_nav' => true, 'active_pattern' => 'driver.history'],
            ['key' => 'driver.profile', 'context' => 'driver', 'section' => null, 'label' => 'Profil', 'route_name' => 'driver.profil', 'icon' => 'user', 'permission_name' => 'driver.task.view', 'sort_order' => 30, 'is_landing' => false, 'show_sidebar' => false, 'show_bottom_nav' => true, 'active_pattern' => 'driver.profil'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['key' => $menu['key']], $menu);
        }
    }
}
