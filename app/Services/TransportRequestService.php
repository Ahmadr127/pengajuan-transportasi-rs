<?php

namespace App\Services;

use App\Models\TransportRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransportRequestService
{
    public function __construct(private readonly RecurringRequestService $recurring) {}

    public function createUmum(User $user, array $data, bool $sampaiSelesai): TransportRequest
    {
        $duplicate = TransportRequest::where('user_id', $user->id)
            ->where('jenis', 'umum')
            ->where('tanggal', $data['tanggal'])
            ->where('jam', $data['jam'])
            ->where('keperluan', $data['keperluan'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'duplicate' => 'Pengajuan yang sama sudah dikirim. Silakan tunggu sebentar.',
            ]);
        }

        if ($sampaiSelesai) {
            $data['tanggal_sampai'] = null;
            $data['jam_sampai'] = null;
        }

        $data['kontak'] = $user->phone ?? '';

        if (! $sampaiSelesai) {
            $data = $this->normalizeTimeRange($data);
        }

        return TransportRequest::create([
            'user_id' => $user->id,
            'jenis' => 'umum',
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'unit_mobil' => null,
            'keperluan' => $data['keperluan'],
            'prioritas' => $data['prioritas'],
            'pemohon_nama' => $user->full_name,
            'pemohon_unit' => $user->unit_kerja,
            'jumlah_penumpang' => null,
            'signature_pemohon' => Str::random(32),
            'signature_pemohon_at' => now(),
            ...collect($data)->except(['keperluan', 'prioritas'])->all(),
        ]);
    }

    public function createAmbulance(User $user, array $data, bool $sampaiSelesai): TransportRequest
    {
        $purpose = $data['purpose'];
        [$alamatAsal, $alamatTujuan] = $this->resolveAmbulanceAddresses($purpose, $data);

        $duplicate = TransportRequest::where('user_id', $user->id)
            ->where('jenis', 'ambulance')
            ->where('tanggal', $data['tanggal'])
            ->where('jam', $data['jam'])
            ->where('pasien_nama', $data['pasien_nama'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'duplicate' => 'Pengajuan yang sama sudah dikirim. Silakan tunggu sebentar.',
            ]);
        }

        if ($sampaiSelesai) {
            $data['tanggal_sampai'] = null;
            $data['jam_sampai'] = null;
        }

        $data['kontak'] = '';

        if (! $sampaiSelesai) {
            $data = $this->normalizeTimeRange($data);
        }

        return TransportRequest::create([
            'user_id' => $user->id,
            'jenis' => 'ambulance',
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'unit_mobil' => null,
            'keperluan' => $purpose,
            'prioritas' => $data['prioritas'],
            ...collect($data)->except(['prioritas'])->all(),
            'alamat_asal' => $alamatAsal,
            'alamat_tujuan' => $alamatTujuan,
            'ruangan' => null,
            'pendamping_nama' => null,
            'kondisi_pasien' => null,
            'signature_pemohon' => Str::random(32),
            'signature_pemohon_at' => now(),
        ]);
    }

    public function createRecurringUmum(User $user, array $data, array $hari): void
    {
        $this->recurring->createUmumTemplate($user, $data, $hari);
    }

    public function createRecurringAmbulance(User $user, array $data, array $hari): void
    {
        [$alamatAsal, $alamatTujuan] = $this->resolveAmbulanceAddresses($data['purpose'], $data);

        $this->recurring->createAmbulanceTemplate($user, $data, $hari, $alamatAsal, $alamatTujuan);
    }

    /**
     * Validasi silang alamat pada pengajuan ambulance (antar/jemput).
     *
     * @return array{0: string, 1: string} [alamatAsal, alamatTujuan]
     */
    private function resolveAmbulanceAddresses(string $purpose, array $data): array
    {
        if ($purpose === 'antar' && empty($data['alamat_tujuan'])) {
            throw ValidationException::withMessages(['alamat_tujuan' => 'Alamat tujuan harus diisi.']);
        }
        if ($purpose === 'jemput' && empty($data['alamat_asal'])) {
            throw ValidationException::withMessages(['alamat_asal' => 'Alamat asal harus diisi.']);
        }

        $alamatAsal = $purpose === 'antar' ? 'RS' : ($data['alamat_asal'] ?? 'RS');
        $alamatTujuan = $purpose === 'antar' ? ($data['alamat_tujuan'] ?? 'RS') : 'RS';

        return [$alamatAsal, $alamatTujuan];
    }

    /**
     * Bila waktu selesai <= waktu mulai, geser tanggal selesai ke hari berikutnya.
     */
    private function normalizeTimeRange(array $data): array
    {
        $mulai = Carbon::parse($data['tanggal'].' '.$data['jam']);
        $sampai = Carbon::parse($data['tanggal_sampai'].' '.$data['jam_sampai']);

        if ($sampai->lte($mulai)) {
            $sampai->addDay();
            $data['tanggal_sampai'] = Carbon::parse($data['tanggal_sampai'])->addDay()->toDateString();
        }

        return $data;
    }
}
