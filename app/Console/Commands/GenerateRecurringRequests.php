<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringTransportTemplate;
use App\Models\TransportRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateRecurringRequests extends Command
{
    protected $signature = 'app:generate-recurring-requests';

    protected $description = 'Generate daily transport requests from active recurring templates';

    public function handle()
    {
        // Gunakan timezone aplikasi agar tidak meleset 1 hari di server UTC
        $today      = Carbon::today(config('app.timezone'));
        $dayOfWeek  = (int) $today->format('N'); // 1=Senin … 7=Minggu

        $activeTemplates = RecurringTransportTemplate::where('is_active', true)
            ->whereDate('start_date', '<=', $today->toDateString())
            ->whereDate('end_date',   '>=', $today->toDateString())
            ->with('user')
            ->get();

        $count = 0;

        foreach ($activeTemplates as $template) {
            // Cek apakah hari ini termasuk hari yang dipilih
            $days = $template->hari ?? [];
            if (!in_array($dayOfWeek, $days)) {
                continue;
            }

            // Cegah duplikasi: cek berdasarkan user_id + jenis + jam + tanggal hari ini
            // (lebih ketat dari sebelumnya yang hanya cek keperluan)
            $existing = TransportRequest::where('user_id', $template->user_id)
                ->where('jenis',    $template->jenis)
                ->where('jam',      $template->jam)
                ->whereDate('tanggal', $today->toDateString())
                ->exists();

            if ($existing) {
                $skipNama = $template->user ? $template->user->full_name : 'user';
                $this->line("Skip (duplikat) template #{$template->id} — {$skipNama} jam {$template->jam}");
                continue;
            }

            // Validasi user masih ada
            if (!$template->user_id || !$template->user) {
                $this->warn("Skip template #{$template->id} — user tidak ditemukan.");
                continue;
            }

            TransportRequest::create([
                'user_id'          => $template->user_id,
                'jenis'            => $template->jenis,
                'nomor_pengajuan'  => TransportRequest::generateNomor(),
                'tanggal'          => $today->toDateString(),
                'jam'              => substr($template->jam, 0, 5),
                // tanggal_sampai & jam_sampai: isi hanya jika template punya jam_sampai
                'tanggal_sampai'   => $template->jam_sampai ? $today->toDateString() : null,
                'jam_sampai'       => $template->jam_sampai ? substr($template->jam_sampai, 0, 5) : null,
                'keperluan'        => $template->keperluan,
                'prioritas'        => $template->prioritas ?? 'biasa',
                'pemohon_nama'     => $template->pemohon_nama ?? $template->user->full_name,
                'pemohon_unit'     => $template->pemohon_unit ?? $template->user->unit_kerja,
                'jumlah_penumpang' => $template->jumlah_penumpang,
                'alamat_asal'      => $template->alamat_asal,
                'alamat_tujuan'    => $template->alamat_tujuan,
                'keterangan'       => $template->keterangan,
                'pasien_nama'      => $template->pasien_nama,
                'pasien_no_rm'     => $template->pasien_no_rm,
                'kontak'           => '',
                'signature_pemohon'    => Str::random(32),
                'signature_pemohon_at' => now(),
            ]);

            $count++;
            $nama = $template->user->full_name ?? "template #{$template->id}";
            $this->info("Dibuat #{$count} — {$nama} ({$template->jenis}, jam {$template->jam})");
        }

        $this->info("✓ Total {$count} pengajuan berulang dibuat untuk hari ini ({$today->format('d/m/Y')}).");

        return Command::SUCCESS;
    }
}
