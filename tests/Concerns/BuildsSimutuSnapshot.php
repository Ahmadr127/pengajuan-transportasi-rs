<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Hash;

trait BuildsSimutuSnapshot
{
    /**
     * Salin fixture snapshot ke file sementara dengan password di-hash memakai
     * konfigurasi hashing aktif (BCRYPT_ROUNDS saat testing), agar cast `hashed`
     * pada model User menerima nilainya.
     *
     * Caller bertanggung jawab menghapus file hasil (tearDown).
     */
    protected function makeSnapshotFixture(): string
    {
        $raw = json_decode((string) file_get_contents(base_path('tests/Fixtures/simutu_organisasi.json')), true);

        foreach ($raw['users'] as &$user) {
            $user['password'] = Hash::make('password');
        }
        unset($user);

        $path = tempnam(sys_get_temp_dir(), 'simutu_org_');
        file_put_contents($path, json_encode($raw));

        return $path;
    }
}
