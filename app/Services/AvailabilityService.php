<?php

namespace App\Services;

use App\Models\TransportRequest;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Perhitungan ketersediaan unit kendaraan untuk sebuah rentang waktu.
 *
 * Catatan perilaku: ada dua strategi berbeda untuk pengajuan yang tidak punya
 * jam akhir (null) — lihat konstanta NULL_* di bawah. Perbedaan ini sengaja
 * dipertahankan karena endpoint cek pengguna dan validasi admin memang berbeda.
 */
class AvailabilityService
{
    /** Akhir waktu null dianggap pukul 23:59 pada tanggal yang sama. */
    public const NULL_END_OF_DAY = 'end_of_day';

    /** Akhir waktu null dianggap 1 jam setelah waktu mulai. */
    public const NULL_PLUS_HOUR = 'plus_hour';

    public function check(
        string $jenis,
        Carbon $mulai,
        Carbon $sampai,
        ?int $excludeId = null,
        string $nullEndStrategy = self::NULL_PLUS_HOUR,
        bool $forceAvailable = false,
    ): array {
        $totalUnits = $this->totalUnits($jenis);

        if ($forceAvailable) {
            return [
                'available' => true,
                'total_units' => $totalUnits,
                'used_units' => 0,
                'available_units' => $totalUnits,
            ];
        }

        $usedUnits = $this->usedUnits($jenis, $mulai, $sampai, $excludeId, $nullEndStrategy);

        return [
            'available' => $usedUnits < $totalUnits,
            'total_units' => $totalUnits,
            'used_units' => $usedUnits,
            'available_units' => max(0, $totalUnits - $usedUnits),
        ];
    }

    public function isAvailable(string $jenis, TransportRequest $transportRequest): bool
    {
        [$mulai, $sampai] = $this->targetWindow($transportRequest);

        return $this->check($jenis, $mulai, $sampai, $transportRequest->id)['available'];
    }

    public function totalUnits(string $jenis): int
    {
        return Vehicle::where('type', $jenis)->where('is_active', true)->count();
    }

    /**
     * Rentang waktu dari string input pengguna (fallback 23:59 bila akhir kosong).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function windowFrom(string $tanggal, string $jam, ?string $tanggalSampai, ?string $jamSampai): array
    {
        if (empty($tanggalSampai) || empty($jamSampai)) {
            $tanggalSampai = $tanggal;
            $jamSampai = '23:59';
        }

        $mulai = Carbon::parse($tanggal.' '.$jam);
        $sampai = Carbon::parse($tanggalSampai.' '.$jamSampai);

        if ($sampai->lte($mulai)) {
            $sampai->addDay();
        }

        return [$mulai, $sampai];
    }

    /**
     * Rentang waktu dari sebuah pengajuan (fallback 23:59 bila tanpa jam akhir).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function targetWindow(TransportRequest $transportRequest): array
    {
        $mulai = Carbon::parse($transportRequest->tanggal->format('Y-m-d').' '.$transportRequest->jam);
        $sampai = ($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
            ? Carbon::parse($transportRequest->tanggal_sampai->format('Y-m-d').' '.$transportRequest->jam_sampai)
            : Carbon::parse($transportRequest->tanggal->format('Y-m-d').' 23:59');

        if ($sampai->lte($mulai)) {
            $sampai->addDay();
        }

        return [$mulai, $sampai];
    }

    public function usedUnits(
        string $jenis,
        Carbon $mulai,
        Carbon $sampai,
        ?int $excludeId = null,
        string $nullEndStrategy = self::NULL_PLUS_HOUR,
    ): int {
        $conflicting = $this->conflicting($jenis, $mulai, $sampai, $excludeId, $nullEndStrategy);

        $assignedUnits = $conflicting->whereNotNull('unit_mobil')->pluck('unit_mobil')->unique()->count();
        $unassignedCount = $conflicting->whereNull('unit_mobil')->count();

        return $assignedUnits + $unassignedCount;
    }

    /**
     * @return Collection<int, int>
     */
    public function busyDriverIds(Carbon $mulai, Carbon $sampai, int $excludeId): Collection
    {
        return TransportRequest::where('status', 'digunakan')
            ->whereNotNull('driver_id')
            ->where('id', '!=', $excludeId)
            ->get()
            ->filter(fn ($r) => $this->overlaps($r, $mulai, $sampai, self::NULL_PLUS_HOUR))
            ->pluck('driver_id')
            ->unique()
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    public function busyVehicleNames(Carbon $mulai, Carbon $sampai, int $excludeId): Collection
    {
        return TransportRequest::where('status', 'digunakan')
            ->whereNotNull('unit_mobil')
            ->where('id', '!=', $excludeId)
            ->get()
            ->filter(fn ($r) => $this->overlaps($r, $mulai, $sampai, self::NULL_PLUS_HOUR))
            ->pluck('unit_mobil')
            ->unique()
            ->values();
    }

    private function conflicting(
        string $jenis,
        Carbon $mulai,
        Carbon $sampai,
        ?int $excludeId,
        string $nullEndStrategy,
    ): Collection {
        $query = TransportRequest::where('jenis', $jenis)->whereIn('status', ['diproses', 'digunakan']);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get()->filter(fn ($r) => $this->overlaps($r, $mulai, $sampai, $nullEndStrategy));
    }

    private function overlaps(TransportRequest $r, Carbon $mulai, Carbon $sampai, string $nullEndStrategy): bool
    {
        $rMulai = Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);

        if ($r->tanggal_sampai && $r->jam_sampai) {
            $rSampai = Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai);
        } elseif ($nullEndStrategy === self::NULL_END_OF_DAY) {
            $rSampai = Carbon::parse($r->tanggal->format('Y-m-d').' 23:59');
        } else {
            $rSampai = $rMulai->copy()->addHour();
        }

        if ($rSampai->lte($rMulai)) {
            $rSampai->addDay();
        }

        return $mulai->lt($rSampai) && $rMulai->lt($sampai);
    }
}
