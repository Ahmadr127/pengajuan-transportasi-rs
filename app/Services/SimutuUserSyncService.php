<?php

namespace App\Services;

use App\Models\User;
use App\Services\Simutu\DatabaseSimutuUserSource;
use App\Services\Simutu\SimutuUserSource;
use App\Services\Simutu\SnapshotSimutuUserSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Menarik data pegawai dari database Simutu (master data) ke tabel users lokal.
 *
 * Prinsip non-destruktif:
 *  - Tidak pernah menghapus user lokal (aman untuk FK transport_requests.user_id).
 *  - role lokal TIDAK ditimpa; hanya diisi saat user baru.
 *  - Password disalin dari simutu (hash bcrypt kompatibel).
 *  - Driver tetap dikelola manual via tabel drivers.
 *
 * Sumber data bisa berupa koneksi DB Simutu (utama) atau snapshot JSON
 * (fallback offline) — lihat resolveSource() dan config `simutu_sync.source`.
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

    /**
     * Pilih sumber data sesuai config `simutu_sync.source`:
     *  - database : paksa pakai koneksi DB Simutu.
     *  - snapshot : paksa pakai file JSON.
     *  - auto     : coba DB Simutu, fallback ke snapshot JSON bila tak tersedia.
     */
    protected function resolveSource(): SimutuUserSource
    {
        $mode = (string) config('simutu_sync.source', 'auto');
        $connection = $this->connection();
        $snapshotPath = (string) config('simutu_sync.organisasi_path');

        if ($mode === 'snapshot') {
            return new SnapshotSimutuUserSource($snapshotPath);
        }

        if ($mode === 'database') {
            return new DatabaseSimutuUserSource($connection);
        }

        try {
            DB::connection($connection)->getPdo();

            return new DatabaseSimutuUserSource($connection);
        } catch (\Throwable $e) {
            Log::warning('Simutu DB tidak tersedia, memakai snapshot JSON: '.$e->getMessage());

            return new SnapshotSimutuUserSource($snapshotPath);
        }
    }

    public function syncForUsername(?string $username): ?User
    {
        if (! $this->enabled() || ! $username) {
            return null;
        }

        try {
            $simutu = $this->resolveSource()->findByUsername($username);
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
        if ($local && $local->roles()->exists()) {
            return (string) $local->roles()->orderBy('roles.id')->value('name'); // jangan timpa role lokal yang sudah diatur admin
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
        $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';

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

        if (! $local) {
            $data['priority_level'] = $this->resolvePriority($simutu);
        }

        if ($local) {
            $local->update($data);

            // User lama yang belum punya role pivot tetap dipetakan, tapi
            // role yang sudah diatur admin tidak ditimpa.
            if ($local->roles()->count() === 0 && $role) {
                $local->syncRolesByName([$role]);
            }

            return $local;
        }

        if (! $createIfMissing) {
            return null;
        }

        if (User::where('username', $data['username'])->exists()) {
            return null;
        }

        $data['priority_level'] = $this->resolvePriority($simutu);

        $user = User::create($data);

        if ($role) {
            $user->syncRolesByName([$role]);
        }

        return $user;
    }

    /**
     * Jalankan sinkronisasi penuh dari sumber yang dipilih config `simutu_sync.source`.
     *
     * @return array{created:int, updated:int, checked:int}
     */
    public function sync(bool $dryRun = false): array
    {
        if (! $this->enabled()) {
            return ['checked' => 0, 'created' => 0, 'updated' => 0];
        }

        return $this->syncFromSource($this->resolveSource(), $dryRun);
    }

    /**
     * Sinkronisasi dari sumber data tertentu (tanpa memeriksa flag `enabled`).
     * Dipakai importer snapshot agar bisa menarik user dari file JSON juga.
     *
     * @return array{created:int, updated:int, checked:int}
     */
    public function syncFromSource(SimutuUserSource $source, bool $dryRun = false): array
    {
        $stats = ['checked' => 0, 'created' => 0, 'updated' => 0];

        foreach ($source->all() as $simutu) {
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
