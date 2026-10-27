<?php

namespace App\Services\Simutu;

use Illuminate\Support\Collection;

/**
 * Sumber data pegawai Simutu.
 *
 * Setiap baris harus menyediakan field berikut agar dapat dikonsumsi
 * SimutuUserSyncService::applyToLocal():
 * simutu_id, nama_lengkap, nip, username, email, password, profesi,
 * status_user, nama_unit, nama_role.
 */
interface SimutuUserSource
{
    /**
     * @return Collection<int, object>
     */
    public function all(): Collection;

    public function findByUsername(string $username): ?object;
}
