<?php

namespace App\Services;

use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * State machine status pengajuan transportasi (admin & driver).
 *
 * Error aturan bisnis dilempar sebagai ValidationException pada key 'status'
 * (atau 'km_awal'/'km_akhir') agar redirect + error bag tetap sama seperti
 * implementasi lama.
 */
class TransportWorkflowService
{
    public function __construct(private readonly AvailabilityService $availability) {}

    /**
     * Perbarui status dari sisi admin. Mengembalikan pesan sukses.
     */
    public function updateByAdmin(TransportRequest $transportRequest, array $data, User $actor, bool $editDigunakan = false): string
    {
        $currentStatus = $transportRequest->status;
        $newStatus = $data['status'] ?? null;

        if ($currentStatus === 'diajukan' && $newStatus === 'diproses') {
            $data = $this->approve($transportRequest, $data, $actor);

        } elseif ($currentStatus === 'digunakan' && $newStatus === 'digunakan' && $editDigunakan) {
            return $this->editDigunakan($transportRequest, $data);

        } elseif ($currentStatus === 'diproses' && $newStatus === 'digunakan') {
            $data = $this->assignUnit($transportRequest, $data);

        } elseif ($currentStatus === 'digunakan' && $newStatus === 'selesai') {
            $data = $this->completeAsAdmin($transportRequest, $data, $actor);

        } elseif ($currentStatus === 'diajukan' && $newStatus === 'tidak_disetujui') {
            // rejection_reason sudah divalidasi di Form Request

        } elseif ($currentStatus === 'digunakan' && $newStatus === 'tidak_disetujui') {
            // rejection_reason sudah divalidasi di Form Request

        } else {
            throw ValidationException::withMessages(['status' => 'Transisi status tidak valid.']);
        }

        $transportRequest->update($data);

        if ($newStatus === 'selesai' && $transportRequest->unit_mobil) {
            Vehicle::where('name', $transportRequest->unit_mobil)
                ->update(['last_km' => $data['km_akhir']]);
        }

        return 'Status pengajuan berhasil diperbarui.';
    }

    public function completeByDriver(TransportRequest $transportRequest, array $data): void
    {
        if ($data['km_akhir'] <= $transportRequest->km_awal) {
            throw ValidationException::withMessages(['km_akhir' => 'KM tiba harus lebih besar dari KM keberangkatan.']);
        }

        $transportRequest->update(array_merge($data, [
            'status' => 'selesai',
            'signature_driver' => $transportRequest->signature_driver ?: Str::random(32),
            'signature_driver_at' => $transportRequest->signature_driver_at ?: now(),
            'signature_pengelola_2' => Str::random(32),
            'signature_pengelola_2_at' => now(),
            'signature_pengelola_2_name' => $transportRequest->signature_pengelola_1_name,
        ]));

        if ($transportRequest->unit_mobil) {
            Vehicle::where('name', $transportRequest->unit_mobil)
                ->update(['last_km' => $data['km_akhir']]);
        }
    }

    public function cancelByDriver(TransportRequest $transportRequest, string $reason): void
    {
        $transportRequest->update([
            'status' => 'tidak_disetujui',
            'rejection_reason' => $reason,
        ]);
    }

    private function approve(TransportRequest $transportRequest, array $data, User $actor): array
    {
        $isPriorityUser = $transportRequest->user && $transportRequest->user->isPriority();

        if (! $isPriorityUser && ! $this->availability->isAvailable($transportRequest->jenis, $transportRequest)) {
            throw ValidationException::withMessages([
                'status' => 'Tidak dapat menyetujui — semua unit kendaraan sudah penuh di waktu tersebut.',
            ]);
        }

        $data['signature_pengelola_1'] = Str::random(32);
        $data['signature_pengelola_1_at'] = now();
        $data['signature_pengelola_1_name'] = $actor->full_name;

        // User prioritas: geser pengajuan user biasa terbaru yang overlap.
        if ($isPriorityUser && ! $this->availability->isAvailable($transportRequest->jenis, $transportRequest)) {
            $this->bumpLowestPriorityOverlap($transportRequest);
        }

        return $data;
    }

    private function editDigunakan(TransportRequest $transportRequest, array $data): string
    {
        if (empty($data['plat_nomor']) && ! empty($data['unit_mobil'])) {
            $vehicle = Vehicle::where('name', $data['unit_mobil'])->first();
            if ($vehicle) {
                $data['plat_nomor'] = $vehicle->plate_number;
            }
        }

        unset($data['status']);
        $transportRequest->update($data);

        return 'Data kendaraan berhasil diperbarui.';
    }

    private function assignUnit(TransportRequest $transportRequest, array $data): array
    {
        $vehicle = Vehicle::where('name', $data['unit_mobil'])->first();

        if ($vehicle && $vehicle->last_km !== null && $data['km_awal'] < $vehicle->last_km) {
            throw ValidationException::withMessages([
                'km_awal' => 'KM berangkat tidak boleh kurang dari '.number_format($vehicle->last_km).' km (KM terakhir kendaraan ini).',
            ]);
        }

        if (empty($data['plat_nomor']) && ! empty($data['unit_mobil']) && $vehicle) {
            $data['plat_nomor'] = $vehicle->plate_number;
        }

        $data['signature_driver'] = Str::random(32);
        $data['signature_driver_at'] = now();

        return $data;
    }

    private function completeAsAdmin(TransportRequest $transportRequest, array $data, User $actor): array
    {
        if ($data['km_akhir'] <= $transportRequest->km_awal) {
            throw ValidationException::withMessages(['km_akhir' => 'KM tiba harus lebih besar dari KM keberangkatan.']);
        }

        $data['signature_pengelola_2'] = Str::random(32);
        $data['signature_pengelola_2_at'] = now();
        $data['signature_pengelola_2_name'] = $actor->full_name;

        return $data;
    }

    private function bumpLowestPriorityOverlap(TransportRequest $transportRequest): void
    {
        [$mulai, $sampai] = $this->availability->targetWindow($transportRequest);

        $overlapping = TransportRequest::where('jenis', $transportRequest->jenis)
            ->where('status', 'diproses')
            ->where('id', '!=', $transportRequest->id)
            ->whereHas('user', fn ($q) => $q->where('priority_level', 0))
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : Carbon::parse($r->tanggal->format('Y-m-d').' 23:59');
                if ($rSampai->lte($rMulai)) {
                    $rSampai->addDay();
                }

                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            })
            ->sortByDesc('created_at');

        $toBump = $overlapping->first();
        if ($toBump) {
            $toBump->update([
                'status' => 'tidak_disetujui',
                'rejection_reason' => 'Dialihkan karena pengajuan dari pengguna prioritas.',
            ]);
        }
    }
}
