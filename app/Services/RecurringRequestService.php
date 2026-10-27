<?php

namespace App\Services;

use App\Models\RecurringTransportTemplate;
use App\Models\TransportRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Pembuatan template pengajuan berulang dan generasi pengajuan harian.
 *
 * Ada DUA jalur generasi yang perilakunya berbeda dan sengaja dipertahankan:
 *  - createTodayRequestFromTemplate(): dipakai saat user membuat template dari
 *    form (tanggal_sampai = hari ini selalu; tanpa guard duplikasi).
 *  - generateDailyFromTemplate(): dipakai scheduler harian (tanggal_sampai hanya
 *    bila ada jam_sampai; dengan guard duplikasi).
 */
class RecurringRequestService
{
    public function createUmumTemplate(User $user, array $data, array $hari): RecurringTransportTemplate
    {
        $template = RecurringTransportTemplate::create([
            'user_id' => $user->id,
            'jenis' => 'umum',
            'keperluan' => $data['keperluan'],
            'prioritas' => $data['prioritas'],
            'pemohon_nama' => $user->full_name,
            'pemohon_unit' => $user->unit_kerja,
            'jumlah_penumpang' => null,
            'jam' => $data['jam'],
            'jam_sampai' => $data['jam_sampai'] ?? null,
            'hari' => $hari,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => '2099-12-31',
            'alamat_tujuan' => $data['alamat_tujuan'],
            'keterangan' => $data['keterangan'] ?? null,
            'is_active' => true,
        ]);

        $this->createTodayRequestFromTemplate($template, $user->phone ?? '');

        return $template;
    }

    public function createAmbulanceTemplate(
        User $user,
        array $data,
        array $hari,
        string $alamatAsal,
        string $alamatTujuan,
    ): RecurringTransportTemplate {
        $template = RecurringTransportTemplate::create([
            'user_id' => $user->id,
            'jenis' => 'ambulance',
            'keperluan' => $data['purpose'],
            'prioritas' => $data['prioritas'],
            'pemohon_nama' => $user->full_name,
            'pemohon_unit' => $user->unit_kerja,
            'jumlah_penumpang' => null,
            'jam' => $data['jam'],
            'jam_sampai' => $data['jam_sampai'] ?? null,
            'hari' => $hari,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => '2099-12-31',
            'alamat_asal' => $alamatAsal,
            'alamat_tujuan' => $alamatTujuan,
            'keterangan' => null,
            'pasien_nama' => $data['pasien_nama'],
            'pasien_no_rm' => $data['pasien_no_rm'] ?? null,
            'is_active' => true,
        ]);

        $this->createTodayRequestFromTemplate($template, $user->phone ?? '');

        return $template;
    }

    public function createTodayRequestFromTemplate(RecurringTransportTemplate $template, string $kontak): ?TransportRequest
    {
        $today = Carbon::today();
        $dayOfWeek = (int) $today->format('N');
        $currentTime = Carbon::now()->format('H:i');

        if (! in_array($dayOfWeek, $template->hari ?? []) || substr($template->jam, 0, 5) <= $currentTime) {
            return null;
        }

        return TransportRequest::create([
            'user_id' => $template->user_id,
            'jenis' => $template->jenis,
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'tanggal' => $today->format('Y-m-d'),
            'jam' => substr($template->jam, 0, 5),
            'tanggal_sampai' => $today->format('Y-m-d'),
            'jam_sampai' => $template->jam_sampai ? substr($template->jam_sampai, 0, 5) : null,
            'keperluan' => $template->keperluan,
            'prioritas' => $template->prioritas,
            'pemohon_nama' => $template->pemohon_nama,
            'pemohon_unit' => $template->pemohon_unit,
            'jumlah_penumpang' => $template->jumlah_penumpang,
            'alamat_asal' => $template->alamat_asal,
            'alamat_tujuan' => $template->alamat_tujuan,
            'keterangan' => $template->keterangan,
            'pasien_nama' => $template->pasien_nama,
            'pasien_no_rm' => $template->pasien_no_rm,
            'kontak' => $kontak,
            'signature_pemohon' => Str::random(32),
            'signature_pemohon_at' => now(),
        ]);
    }

    /**
     * Generasi harian dari scheduler.
     *
     * @return array{status: string, request: ?TransportRequest}
     *                                                           status: created | skipped_day | skipped_duplicate | skipped_no_user
     */
    public function generateDailyFromTemplate(RecurringTransportTemplate $template): array
    {
        $today = Carbon::today(config('app.timezone'));
        $dayOfWeek = (int) $today->format('N');

        if (! in_array($dayOfWeek, $template->hari ?? [])) {
            return ['status' => 'skipped_day', 'request' => null];
        }

        $existing = TransportRequest::where('user_id', $template->user_id)
            ->where('jenis', $template->jenis)
            ->where('jam', $template->jam)
            ->whereDate('tanggal', $today->toDateString())
            ->exists();

        if ($existing) {
            return ['status' => 'skipped_duplicate', 'request' => null];
        }

        if (! $template->user_id || ! $template->user) {
            return ['status' => 'skipped_no_user', 'request' => null];
        }

        $request = TransportRequest::create([
            'user_id' => $template->user_id,
            'jenis' => $template->jenis,
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'tanggal' => $today->toDateString(),
            'jam' => substr($template->jam, 0, 5),
            'tanggal_sampai' => $template->jam_sampai ? $today->toDateString() : null,
            'jam_sampai' => $template->jam_sampai ? substr($template->jam_sampai, 0, 5) : null,
            'keperluan' => $template->keperluan,
            'prioritas' => $template->prioritas ?? 'biasa',
            'pemohon_nama' => $template->pemohon_nama ?? $template->user->full_name,
            'pemohon_unit' => $template->pemohon_unit ?? $template->user->unit_kerja,
            'jumlah_penumpang' => $template->jumlah_penumpang,
            'alamat_asal' => $template->alamat_asal,
            'alamat_tujuan' => $template->alamat_tujuan,
            'keterangan' => $template->keterangan,
            'pasien_nama' => $template->pasien_nama,
            'pasien_no_rm' => $template->pasien_no_rm,
            'kontak' => '',
            'signature_pemohon' => Str::random(32),
            'signature_pemohon_at' => now(),
        ]);

        return ['status' => 'created', 'request' => $request];
    }
}
