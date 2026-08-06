<x-app-layout title="Detail Tugas — SIPETRANS">
    <div class="mx-auto px-3 pt-3 pb-5" style="max-width:42rem;">

        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-900 mb-0" style="font-size:1rem;">Detail Pengajuan</h1>
                <p class="text-xxs text-slate-500 font-mono mt-1 mb-0">{{ $transportRequest->nomor_pengajuan }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php
                    $statusCfg = match($transportRequest->status) {
                        'digunakan' => ['badge-cyan',    'Digunakan'],
                        'selesai'   => ['badge-emerald', 'Selesai'],
                        default     => ['badge-slate',   ucfirst($transportRequest->status)],
                    };
                @endphp
                <span class="badge {{ $statusCfg[0] }} d-inline-flex align-items-center rounded-pill text-xxs fw-bold">
                    {{ $statusCfg[1] }}
                </span>
                <a href="{{ route('driver.print', $transportRequest) }}?from=driver" target="_blank"
                   class="d-inline-flex align-items-center gap-1 rounded px-3 py-2 text-xs fw-600"
                   style="background-color:#00685E; color:white !important;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </a>
                <a href="{{ route('driver.dashboard') }}"
                   class="d-inline-flex align-items-center rounded border border-slate-300 bg-white px-3 py-2 text-xs fw-600 text-slate-700">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info Pengajuan -->
        <div class="sp-card overflow-hidden mb-3">
            <div class="sp-card-header">
                <p class="text-xxs fw-600 text-slate-600 text-uppercase tracking-wide mb-0">Informasi Pengajuan</p>
            </div>
            <dl class="px-3 py-2 text-xs mb-0">
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Jenis</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ ucfirst($transportRequest->jenis) }}
                        @if($transportRequest->prioritas === 'segera')
                            <span class="badge badge-red d-inline-flex align-items-center rounded-pill text-xxs fw-bold ms-1">CITO</span>
                        @endif
                    </dd>
                </div>
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Pemohon</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}
                        <span class="text-slate-500 fw-normal"> · {{ $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit }}</span>
                    </dd>
                </div>
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Jadwal</dt>
                    <dd class="fw-500 text-slate-800 mb-0">
                        {{ $transportRequest->tanggal->format('d/m/Y') }} {{ substr($transportRequest->jam, 0, 5) }}
                        <span class="text-slate-400">–</span>
                        @if($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
                            {{ $transportRequest->tanggal_sampai->format('d/m/Y') }} {{ substr($transportRequest->jam_sampai, 0, 5) }}
                        @else
                            Sampai Selesai
                        @endif
                    </dd>
                </div>
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Tgl Dibuat</dt>
                    <dd class="text-slate-500 mb-0">{{ $transportRequest->created_at->format('d/m/Y, H:i') }}</dd>
                </div>
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Unit Kendaraan</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->unit_mobil ?? '-' }}
                        @if($transportRequest->plat_nomor)
                            <span class="text-slate-500 fw-normal font-mono"> ({{ $transportRequest->plat_nomor }})</span>
                        @endif
                    </dd>
                </div>
                @if($transportRequest->alamat_tujuan)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Tujuan</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->alamat_tujuan }}</dd>
                </div>
                @endif
                @if($transportRequest->alamat_asal && $transportRequest->alamat_asal !== 'RS')
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Asal</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->alamat_asal }}</dd>
                </div>
                @endif
                @if($transportRequest->keperluan)
                <div class="d-flex gap-2 py-2">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Keperluan</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->keperluan }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($transportRequest->jenis === 'ambulance' && $transportRequest->pasien_nama)
        <!-- Info Pasien -->
        <div class="sp-card overflow-hidden mb-3">
            <div class="sp-card-header">
                <p class="text-xxs fw-600 text-slate-600 text-uppercase tracking-wide mb-0">Identitas Pasien</p>
            </div>
            <dl class="px-3 py-2 text-xs mb-0">
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Nama</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->pasien_nama }}</dd>
                </div>
                @if($transportRequest->pasien_no_rm)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">No. RM</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->pasien_no_rm }}</dd>
                </div>
                @endif
                @if($transportRequest->alamat_pasien)
                <div class="d-flex gap-2 py-2">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Alamat</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->alamat_pasien }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

        <!-- Info Perjalanan -->
        @if($transportRequest->km_awal || $transportRequest->km_akhir || $transportRequest->jam_kedatangan || $transportRequest->biaya_tol)
        <div class="sp-card overflow-hidden mb-3">
            <div class="sp-card-header">
                <p class="text-xxs fw-600 text-slate-600 text-uppercase tracking-wide mb-0">Data Perjalanan</p>
            </div>
            <dl class="px-3 py-2 text-xs mb-0">
                @if($transportRequest->km_awal)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">KM Berangkat</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ number_format($transportRequest->km_awal, 0, ',', '.') }} km</dd>
                </div>
                @endif
                @if($transportRequest->km_akhir)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">KM Tiba</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ number_format($transportRequest->km_akhir, 0, ',', '.') }} km</dd>
                </div>
                @endif
                @if($transportRequest->km_awal && $transportRequest->km_akhir)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Total Jarak</dt>
                    <dd class="fw-600 text-emerald-700 mb-0">{{ number_format($transportRequest->km_akhir - $transportRequest->km_awal, 0, ',', '.') }} km</dd>
                </div>
                @endif
                @if($transportRequest->jam_kedatangan)
                <div class="d-flex gap-2 py-2 border-bottom border-slate-100">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Jam Tiba</dt>
                    <dd class="fw-500 text-slate-800 mb-0">{{ $transportRequest->jam_kedatangan }}</dd>
                </div>
                @endif
                @if($transportRequest->biaya_tol)
                <div class="d-flex gap-2 py-2">
                    <dt class="text-slate-500 shrink-0" style="width:7rem;">Biaya Tol</dt>
                    <dd class="fw-600 text-slate-800 mb-0">Rp {{ number_format($transportRequest->biaya_tol, 0, ',', '.') }}</dd>
                </div>
                @endif
            </dl>
        </div>
        @endif

    </div>
</x-app-layout>
