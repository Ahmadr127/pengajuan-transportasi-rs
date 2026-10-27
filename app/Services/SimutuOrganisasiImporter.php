<?php

namespace App\Services;

use App\Models\JobRole;
use App\Models\Unit;
use App\Services\Simutu\SnapshotSimutuUserSource;
use RuntimeException;

/**
 * Mengimpor data dari snapshot JSON Simutu: master Unit & Jabatan (`units`,
 * `roles`) lalu akun pegawai (`users`).
 *
 * Bersifat idempoten (kunci: `simutu_id`), dan sinkronisasi user memakai
 * prinsip non-destruktif yang sama seperti SimutuUserSyncService (tidak
 * pernah menghapus, role lokal tidak ditimpa).
 */
class SimutuOrganisasiImporter
{
    public function __construct(private readonly SimutuUserSyncService $users)
    {
    }

    /**
     * @return array{units:int, roles:int, users_created:int, users_updated:int, users_checked:int, path:string}
     */
    public function import(?string $path = null): array
    {
        $path = $path ?: (string) config('simutu_sync.organisasi_path');

        if (! is_file($path)) {
            throw new RuntimeException("File organisasi Simutu tidak ditemukan: {$path}");
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data)) {
            throw new RuntimeException("File organisasi Simutu tidak valid (JSON): {$path}");
        }

        $units = $this->importUnits($data['units'] ?? []);
        $roles = $this->importJobRoles($data['roles'] ?? []);

        // Setelah master unit & jabatan tersedia, tarik akun pegawai dari snapshot yang sama.
        $userStats = $this->users->syncFromSource(new SnapshotSimutuUserSource($path));

        return [
            'units' => $units,
            'roles' => $roles,
            'users_created' => $userStats['created'],
            'users_updated' => $userStats['updated'],
            'users_checked' => $userStats['checked'],
            'path' => $path,
        ];
    }

    /**
     * @param  array<int, mixed>  $rows
     */
    private function importUnits(array $rows): int
    {
        $count = 0;

        foreach ($rows as $unit) {
            if (! is_array($unit) || empty($unit['kode_unit'])) {
                continue;
            }

            Unit::updateOrCreate(
                $this->key($unit, 'kode_unit'),
                [
                    'simutu_id' => isset($unit['id']) ? (string) $unit['id'] : null,
                    'kode_unit' => $unit['kode_unit'],
                    'nama_unit' => $unit['nama_unit'] ?? '',
                    'deskripsi_unit' => $unit['deskripsi_unit'] ?? null,
                    'status_unit' => $unit['status_unit'] ?? 'aktif',
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * @param  array<int, mixed>  $rows
     */
    private function importJobRoles(array $rows): int
    {
        $count = 0;

        foreach ($rows as $role) {
            if (! is_array($role) || empty($role['nama_role'])) {
                continue;
            }

            JobRole::updateOrCreate(
                $this->key($role, 'nama_role'),
                [
                    'simutu_id' => isset($role['id']) ? (string) $role['id'] : null,
                    'nama_role' => $role['nama_role'],
                    'deskripsi_role' => $role['deskripsi_role'] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Kunci pencocokan: pakai simutu_id bila ada, jika tidak pakai field unik lokal.
     */
    private function key(array $row, string $fallbackColumn): array
    {
        if (! empty($row['id'])) {
            return ['simutu_id' => (string) $row['id']];
        }

        return [$fallbackColumn => $row[$fallbackColumn]];
    }
}
