<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Menarik data pegawai dari database Simutu (master data) ke tabel users lokal.
 *
 * Prinsip non-destruktif:
 *  - Tidak pernah menghapus user lokal (aman untuk FK transport_requests.user_id).
 *  - role ('user'/'admin'/'driver') lokal TIDAK ditimpa; hanya diisi saat user baru.
 *  - Password disalin dari simutu (hash bcrypt kompatibel).
 *  - Driver tetap dikelola manual via tabel drivers.
 */
class SimutuUserSyncService
{
    public function enabled(): bool
    {
        return (bool) config('simutu_sync.enabled', true);
    }

    protected function connection(): string
    {
        return (string) config('simutu_sync.db_connection', 'simutu');
    }

    protected function fetchSimutuUsers(): \Illuminate\Support\Collection
    {
        return DB::connection($this->connection())
            ->table('users')
            ->leftJoin('tbl_unit', 'users.unit_id', '=', 'tbl_unit.id')
            ->leftJoin('tbl_role', 'users.role_id', '=', 'tbl_role.id')
            ->select(
                'users.id as simutu_id',
                'users.nama_lengkap',
                'users.nip',
                'users.username',
                'users.email',
                'users.password',
                'users.profesi',
                'users.status_user',
                'tbl_unit.nama_unit',
                'tbl_role.nama_role'
            )
            ->get();
    }

    public function syncForUsername(?string $username): ?User
    {
        if (! $this->enabled() || ! $username) {
            return null;
        }

        try {
            $simutu = DB::connection($this->connection())
                ->table('users')
                ->leftJoin('tbl_unit', 'users.unit_id', '=', 'tbl_unit.id')
                ->leftJoin('tbl_role', 'users.role_id', '=', 'tbl_role.id')
                ->select(
                    'users.id as simutu_id',
                    'users.nama_lengkap',
                    'users.nip',
                    'users.username',
                    'users.email',
                    'users.password',
                    'users.profesi',
                    'users.status_user',
                    'tbl_unit.nama_unit',
                    'tbl_role.nama_role'
                )
                ->where('users.username', $username)
                ->first();
        } catch (\Throwable $e) {
            Log::warning('Simutu syncForUsername gagal: '.$e->getMessage());
            return null;
        }

        if (! $simutu) {
            return null;
        }

        $local = $this->findLocalUser((array) $simutu);
        return $this->applyToLocal((array) $simutu, $local, createIfMissing: true);
    }

    protected function findLocalUser(array $simutu): ?User
    {
        if (! empty($simutu['simutu_id'])) {
            $bySimutuId = User::where('simutu_id', $simutu['simutu_id'])->first();
            if ($bySimutuId) {
                return $bySimutuId;
            }
        }

        if (! empty($simutu['username'])) {
            $byUsername = User::where('username', $simutu['username'])->first();
            if ($byUsername) {
                return $byUsername;
            }
        }

        if (! empty($simutu['nip'])) {
            $byNip = User::where('nip', $simutu['nip'])->first();
            if ($byNip) {
                return $byNip;
            }
        }

        return null;
    }

    protected function resolveRole(array $simutu, ?User $local): string
    {
        if ($local && $local->role) {
            return $local->role; // jangan timpa role lokal yang sudah diatur admin
        }

        return (string) config('simutu_sync.default_role', 'user');
    }

    protected function resolvePriority(array $simutu): int
    {
        $namaRole = strtoupper($simutu['nama_role'] ?? '');
        foreach (config('simutu_sync.priority_roles', []) as $needle) {
            if (str_contains($namaRole, $needle)) {
                return 1;
            }
        }

        return 0;
    }

    protected function splitName(string $namaLengkap): array
    {
        $parts = preg_split('/\s+/', trim($namaLengkap));
        $first = $parts[0] ?? '';
        $last  = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

        return [$first, $last];
    }

    protected function applyToLocal(array $simutu, ?User $local, bool $createIfMissing): ?User
    {
        [$first, $last] = $this->splitName($simutu['nama_lengkap'] ?? '');

        $data = [
            'first_name' => $first ?: ($local?->first_name),
            'last_name' => $last ?: ($local?->last_name),
            'username' => $simutu['username'] ?? ($local?->username),
            'nip' => ($simutu['nip'] ?? null) ?: ($local?->nip),
            'unit_kerja' => ($simutu['nama_unit'] ?? null) ?: ($local?->unit_kerja),
            'profesi' => ($simutu['profesi'] ?? null) ?: ($local?->profesi),
            'jabatan' => ($simutu['nama_role'] ?? null) ?: ($local?->jabatan),
            'simutu_id' => $simutu['simutu_id'] ?? null,
            'simutu_status' => $simutu['status_user'] ?? null,
            'simutu_synced_at' => now(),
        ];

        // Posisi pekerjaan tidak tersedia di simutu -> pertahankan nilai lama.
        if (! $local || blank($local->posisi_pekerjaan)) {
            $data['posisi_pekerjaan'] = $simutu['nama_role'] ?? null;
        }

        if (config('simutu_sync.sync_password', true) && ! empty($simutu['password'])) {
            $data['password'] = $simutu['password'];
        }

        $role = $this->resolveRole($simutu, $local);
        if ($role) {
            $data['role'] = $role;
        }
        if (! $local) {
            $data['priority_level'] = $this->resolvePriority($simutu);
        }

        if ($local) {
            $local->update($data);
            return $local;
        }

        if (! $createIfMissing) {
            return null;
        }

        if (User::where('username', $data['username'])->exists()) {
            return null;
        }

        $data['priority_level'] = $this->resolvePriority($simutu);

        return User::create($data);
    }

    /**
     * Jalankan sinkronisasi penuh.
     *
     * @return array{created:int, updated:int, checked:int}
     */
    public function sync(bool $dryRun = false): array
    {
        $stats = ['checked' => 0, 'created' => 0, 'updated' => 0];

        if (! $this->enabled()) {
            return $stats;
        }

        foreach ($this->fetchSimutuUsers() as $simutu) {
            $stats['checked']++;
            $simutuArr = (array) $simutu;
            $local = $this->findLocalUser($simutuArr);

            if (! $local) {
                if ($dryRun) {
                    $stats['created']++;
                } else {
                    $user = $this->applyToLocal($simutuArr, null, createIfMissing: true);
                    if ($user) {
                        $stats['created']++;
                    }
                }
                continue;
            }

            $fresh = $this->applyToLocal($simutuArr, $local, createIfMissing: false);
            if ($fresh && $fresh->simutu_synced_at) {
                $stats['updated']++;
            }
        }

        return $stats;
    }
}