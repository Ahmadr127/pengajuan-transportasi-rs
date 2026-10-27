<?php

namespace App\Services\Simutu;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Sumber data pegawai langsung dari database Simutu (koneksi read-only).
 */
class DatabaseSimutuUserSource implements SimutuUserSource
{
    public function __construct(private readonly string $connection)
    {
    }

    public function all(): Collection
    {
        return $this->baseQuery()->get();
    }

    public function findByUsername(string $username): ?object
    {
        return $this->baseQuery()->where('users.username', $username)->first();
    }

    private function baseQuery(): Builder
    {
        return DB::connection($this->connection)
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
            );
    }
}
