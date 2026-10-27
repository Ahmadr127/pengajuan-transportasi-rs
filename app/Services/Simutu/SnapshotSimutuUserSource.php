<?php

namespace App\Services\Simutu;

use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Sumber data pegawai dari snapshot JSON Simutu (mode offline / DB tak tersedia).
 *
 * Mapping unit_id → nama_unit dan role_id → nama_role diambil dari array
 * `units` dan `roles` di file yang sama, sehingga sumber ini mandiri
 * (tidak butuh koneksi DB Simutu maupun master lokal).
 */
class SnapshotSimutuUserSource implements SimutuUserSource
{
    /** @var array<string, mixed>|null */
    private ?array $data = null;

    /** @var array<string, string|null>|null */
    private ?array $unitMap = null;

    /** @var array<string, string|null>|null */
    private ?array $roleMap = null;

    public function __construct(private readonly string $path)
    {
    }

    public function all(): Collection
    {
        return collect($this->users())->map(fn ($user) => $this->mapUser($user));
    }

    public function findByUsername(string $username): ?object
    {
        foreach ($this->users() as $user) {
            if ((string) ($user['username'] ?? '') === $username) {
                return $this->mapUser($user);
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function users(): array
    {
        return $this->data()['users'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    private function data(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        if (! is_file($this->path)) {
            throw new RuntimeException("Snapshot Simutu tidak ditemukan: {$this->path}");
        }

        $decoded = json_decode((string) file_get_contents($this->path), true);

        if (! is_array($decoded)) {
            throw new RuntimeException("Snapshot Simutu tidak valid (JSON): {$this->path}");
        }

        return $this->data = $decoded;
    }

    private function mapUser(array $user): object
    {
        return (object) [
            'simutu_id' => isset($user['id']) ? (string) $user['id'] : null,
            'nama_lengkap' => $user['nama_lengkap'] ?? null,
            'nip' => $user['nip'] ?? null,
            'username' => $user['username'] ?? null,
            'email' => $user['email'] ?? null,
            'password' => $user['password'] ?? null,
            'profesi' => $user['profesi'] ?? null,
            'status_user' => $user['status_user'] ?? null,
            'nama_unit' => $this->unitMap()[(string) ($user['unit_id'] ?? '')] ?? null,
            'nama_role' => $this->roleMap()[(string) ($user['role_id'] ?? '')] ?? null,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function unitMap(): array
    {
        if ($this->unitMap !== null) {
            return $this->unitMap;
        }

        return $this->unitMap = collect($this->data()['units'] ?? [])
            ->filter(fn ($row) => is_array($row) && isset($row['id']))
            ->mapWithKeys(fn ($row) => [(string) $row['id'] => $row['nama_unit'] ?? null])
            ->all();
    }

    /**
     * @return array<string, string|null>
     */
    private function roleMap(): array
    {
        if ($this->roleMap !== null) {
            return $this->roleMap;
        }

        return $this->roleMap = collect($this->data()['roles'] ?? [])
            ->filter(fn ($row) => is_array($row) && isset($row['id']))
            ->mapWithKeys(fn ($row) => [(string) $row['id'] => $row['nama_role'] ?? null])
            ->all();
    }
}
