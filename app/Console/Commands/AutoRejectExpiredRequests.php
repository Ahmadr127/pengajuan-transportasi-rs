<?php

namespace App\Console\Commands;

use App\Models\TransportRequest;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoRejectExpiredRequests extends Command
{
    protected $signature = 'transport:auto-reject-expired';

    protected $description = 'Otomatis menolak pengajuan yang tidak diproses/dieksekusi dalam 24 jam';

    public function handle()
    {
        $now      = Carbon::now(config('app.timezone'));
        $deadline = $now->copy()->subHours(24);
        $count    = 0;

        // ── 1. Status "diajukan" ──────────────────────────────────────────────
        // Tolak jika sudah lebih dari 24 jam sejak dibuat dan belum diproses admin.
        $pendingExpired = TransportRequest::where('status', 'diajukan')
            ->where('created_at', '<=', $deadline->toDateTimeString())
            ->get();

        foreach ($pendingExpired as $req) {
            $req->update([
                'status'           => 'tidak_disetujui',
                'rejection_reason' => 'Pengajuan otomatis ditolak karena tidak diproses oleh admin dalam 24 jam sejak pengajuan dibuat.',
            ]);
            $count++;
            $nama = $req->user?->full_name ?? $req->pemohon_nama;
            $this->info("Ditolak (tidak diproses) #{$req->nomor_pengajuan} — {$nama}");
        }

        // ── 2. Status "diproses" ──────────────────────────────────────────────
        // Tolak jika waktu keberangkatan (tanggal + jam) sudah lewat lebih dari 24 jam
        // dan belum ditandai digunakan oleh admin.
        $approvedAll = TransportRequest::where('status', 'diproses')->get();

        foreach ($approvedAll as $req) {
            // Bangun datetime keberangkatan dengan timezone yang benar
            $jadwalDt = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $req->tanggal->format('Y-m-d') . ' ' . substr($req->jam, 0, 5) . ':00',
                config('app.timezone')
            );

            // Lewat 24 jam sejak jadwal keberangkatan?
            if ($now->gt($jadwalDt->copy()->addHours(24))) {
                $jadwal = $req->tanggal->format('d/m/Y') . ' ' . substr($req->jam, 0, 5);
                $req->update([
                    'status'           => 'tidak_disetujui',
                    'rejection_reason' => "Pengajuan otomatis ditolak karena transportasi yang dijadwalkan pada {$jadwal} tidak dieksekusi dalam 24 jam setelah waktu keberangkatan.",
                ]);
                $count++;
                $nama = $req->user?->full_name ?? $req->pemohon_nama;
                $this->info("Ditolak (tidak dieksekusi) #{$req->nomor_pengajuan} — {$nama} (jadwal: {$jadwal})");
            }
        }

        if ($count > 0) {
            $this->info("✓ Total {$count} pengajuan otomatis ditolak.");
        } else {
            $this->info("✓ Tidak ada pengajuan yang perlu ditolak.");
        }

        return Command::SUCCESS;
    }
}
