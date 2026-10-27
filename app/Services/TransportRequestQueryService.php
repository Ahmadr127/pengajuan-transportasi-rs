<?php

namespace App\Services;

use App\Models\TransportRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Query builder untuk daftar & laporan pengajuan.
 *
 * Menerima array filter (bukan Request) agar dapat dipakai ulang oleh API.
 */
class TransportRequestQueryService
{
    public function adminIndex(array $filters): LengthAwarePaginator
    {
        $query = TransportRequest::with('user');

        $hasStatus = array_key_exists('status', $filters);
        $selectedStatus = $filters['status'] ?? null;

        if ($hasStatus && ! blank($selectedStatus)) {
            $fifoStatuses = ['diajukan', 'diproses', 'digunakan'];

            if (in_array($selectedStatus, $fifoStatuses)) {
                $query->leftJoin('users', 'transport_requests.user_id', '=', 'users.id')
                    ->orderByRaw('COALESCE(users.priority_level, 0) DESC')
                    ->orderByRaw("CONCAT(transport_requests.tanggal, ' ', transport_requests.jam) ASC")
                    ->select('transport_requests.*');
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $query->where('status', $selectedStatus);
        } elseif ($hasStatus) {
            // "Semua Status" dipilih.
            $query->orderBy('created_at', 'desc');
        } else {
            // Default: tampilkan semua status.
            $query->orderBy('created_at', 'desc');
        }

        if (! blank($filters['jenis'] ?? null)) {
            $query->where('jenis', $filters['jenis']);
        }

        if (! blank($filters['tanggal'] ?? null)) {
            $query->whereDate('tanggal', $filters['tanggal']);
        }

        return $query->paginate(10)->withQueryString();
    }

    public function laporanQuery(array $filters): Builder
    {
        $query = TransportRequest::with(['user', 'driver.user'])->latest();
        $this->applyLaporanFilters($query, $filters);

        return $query;
    }

    public function applyLaporanFilters(Builder $query, array $filters): void
    {
        $like = fn ($val) => '%'.strtolower($val).'%';

        if (! blank($filters['nomor'] ?? null)) {
            $query->whereRaw('LOWER(nomor_pengajuan) LIKE ?', [$like($filters['nomor'])]);
        }
        if (! blank($filters['jenis'] ?? null)) {
            $query->where('jenis', $filters['jenis']);
        }
        if (! blank($filters['status'] ?? null)) {
            $query->where('status', $filters['status']);
        }
        if (! blank($filters['prioritas'] ?? null)) {
            $query->where('prioritas', $filters['prioritas']);
        }
        if (! blank($filters['nip_pemohon'] ?? null)) {
            $query->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nip) LIKE ?', [$like($filters['nip_pemohon'])]));
        }
        if (! blank($filters['pemohon'] ?? null)) {
            $query->whereHas('user', fn ($q) => $q->whereRaw('LOWER(first_name) LIKE ?', [$like($filters['pemohon'])])
                ->orWhereRaw('LOWER(last_name) LIKE ?', [$like($filters['pemohon'])]));
        }
        if (! blank($filters['unit_kerja'] ?? null)) {
            $query->whereHas('user', fn ($q) => $q->whereRaw('LOWER(unit_kerja) LIKE ?', [$like($filters['unit_kerja'])]));
        }
        if (! blank($filters['keperluan'] ?? null)) {
            $query->whereRaw('LOWER(keperluan) LIKE ?', [$like($filters['keperluan'])]);
        }
        if (! blank($filters['tujuan'] ?? null)) {
            $query->whereRaw('LOWER(alamat_tujuan) LIKE ?', [$like($filters['tujuan'])]);
        }
        if (! blank($filters['supir'] ?? null)) {
            $query->whereHas('driver', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [$like($filters['supir'])]));
        }
        if (! blank($filters['unit_mobil'] ?? null)) {
            $query->whereRaw('LOWER(unit_mobil) LIKE ?', [$like($filters['unit_mobil'])]);
        }
        if (! blank($filters['plat_nomor'] ?? null)) {
            $query->whereRaw('LOWER(plat_nomor) LIKE ?', [$like($filters['plat_nomor'])]);
        }
        if (! blank($filters['tanggal_dari'] ?? null)) {
            $query->whereDate('tanggal', '>=', $filters['tanggal_dari']);
        }
        if (! blank($filters['tanggal_sampai_filter'] ?? null)) {
            $query->whereDate('tanggal', '<=', $filters['tanggal_sampai_filter']);
        }
    }
}
