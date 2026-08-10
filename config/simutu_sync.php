<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi User dari Simutu
    |--------------------------------------------------------------------------
    | Simutu adalah satu-satunya sumber data pegawai (master data).
    | Sistem ini MENARIK data dari simutu dan memperbarui tabel users lokal.
    |
    | Prinsip non-destruktif:
    |  - Tidak pernah menghapus user lokal (ada relasi FK user_id / driver_id).
    |  - Role lokal (user/admin/driver) TIDAK ditimpa untuk user lama;
    |    hanya diisi saat user baru.
    |  - Password disalin dari simutu (hash bcrypt kompatibel), satu akun satu password.
    |  - Role "driver" tetap dikelola manual via tabel drivers; sync tidak membuat driver.
    */

    'enabled' => (bool) env('SIMUTU_SYNC_ENABLED', true),

    'db_connection' => env('SIMUTU_DB_CONNECTION', 'simutu'),

    'sync_password' => true,

    'block_login_statuses' => ['non-aktif'],

    // Data user dianggap basi setelah sekian detik -> lazy-sync saat login.
    'stale_after_seconds' => 3600,

    // Role default untuk user BARU dari simutu.
    'default_role' => 'user',

    // Priority level 1 untuk jabatan berikut (simutu tbl_role.nama_role).
    'priority_roles' => [
        'KOORDINATOR',
        'MANAGER',
        'KEPALA',
        'SUPERVISOR',
    ],
];