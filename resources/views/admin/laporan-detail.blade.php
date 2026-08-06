<x-app-layout title="Detail Laporan — SIPETRANS">
    <div class="container-fluid px-3 px-sm-4 pt-4 pb-5" style="max-width:56rem;">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between gap-3 border-bottom border-slate-200 pb-3 mb-4">
            <div>
                <h1 class="fs-5 fw-bold text-slate-800 mb-0">Detail Laporan Pengajuan</h1>
                <p class="text-xs text-slate-500 mt-1 mb-0">{{ $transportRequest->nomor_pengajuan }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($transportRequest->status === 'selesai')
                    <a href="{{ route('admin.transport.print', $transportRequest) }}" target="_blank"
                       class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                        Print
                    </a>
                @endif
                <a href="{{ route('admin.laporan') }}"
                   class="btn btn-sm btn-outline-secondary fw-600">
                    Kembali
                </a>
            </div>
        </div>

        @php
            $badgeMap = [
                'diajukan'        => 'badge-amber',
                'diproses'        => 'badge-blue',
                'digunakan'       => 'badge-cyan',
                'selesai'         => 'badge-emerald',
                'tidak_disetujui' => 'badge-red',
            ];
            $statusLabel = match($transportRequest->status) {
                'diproses'        => 'Disetujui',
                'digunakan'       => 'Digunakan',
                'tidak_disetujui' => 'Tidak Disetujui',
                default           => ucfirst($transportRequest->status),
            };
        @endphp

        <div class="row g-3">

            {{-- Informasi Pengajuan --}}
            <div class="col-12 col-lg-6">
                <div class="sp-card overflow-hidden h-100">
                    <div class="sp-card-header d-flex align-items-center justify-content-between">
                        <h2 class="text-xs fw-600 text-slate-800 mb-0">Informasi Pengajuan</h2>
                        <span class="badge status-badge {{ $badgeMap[$transportRequest->status] ?? 'badge-slate' }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <dl class="px-3 py-2 text-xs mb-0">
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">No. Pengajuan</dt>
                            <dd class="font-mono fw-600 text-slate-800 mb-0">{{ $transportRequest->nomor_pengajuan }}</dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Dibuat</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->created_at->format('d/m/Y, H:i') }}</dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Jenis</dt>
                            <dd class="text-slate-800 fw-500 mb-0">
                                {{ ucfirst($transportRequest->jenis) }}
                                @if($transportRequest->keperluan)
                                    <span class="text-slate-500">({{ ucfirst($transportRequest->keperluan) }})</span>
                                @endif
                                @if($transportRequest->prioritas === 'segera')
                                    <span class="badge status-badge badge-red ms-1">CITO</span>
                                @endif
                            </dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Tanggal</dt>
                            <dd class="text-slate-800 fw-500 mb-0">
                                {{ $transportRequest->tanggal->format('d/m/Y') }} {{ substr($transportRequest->jam, 0, 5) }}
                                @if($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
                                    &ndash; {{ $transportRequest->tanggal_sampai->format('d/m/Y') }} {{ substr($transportRequest->jam_sampai, 0, 5) }}
                                @else
                                    &ndash; <span class="text-slate-500">Sampai Selesai</span>
                                @endif
                            </dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Tujuan</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->alamat_tujuan ?? '-' }}</dd>
                        </div>
                        @if($transportRequest->alamat_asal)
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Asal</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->alamat_asal }}</dd>
                        </div>
                        @endif
                        @if($transportRequest->keterangan)
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Keterangan</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->keterangan }}</dd>
                        </div>
                        @endif
                        @if($transportRequest->status === 'tidak_disetujui' && $transportRequest->rejection_reason)
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Alasan Tolak</dt>
                            <dd class="text-red-700 mb-0">{{ $transportRequest->rejection_reason }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Pemohon --}}
            <div class="col-12 col-lg-6">
                <div class="sp-card overflow-hidden h-100">
                    <div class="sp-card-header">
                        <h2 class="text-xs fw-600 text-slate-800 mb-0">Data Pemohon</h2>
                    </div>
                    <dl class="px-3 py-2 text-xs mb-0">
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Nama</dt>
                            <dd class="text-slate-800 fw-500 mb-0">
                                {{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}
                                @if($transportRequest->user && $transportRequest->user->isPriority())
                                    <span class="badge ms-1" style="font-size:.5625rem;background:#ede9fe;color:#6d28d9;">PRIORITAS</span>
                                @endif
                            </dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Unit Kerja</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit ?? '-' }}</dd>
                        </div>
                        @if($transportRequest->user)
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">NIP</dt>
                            <dd class="text-slate-700 font-mono mb-0">{{ $transportRequest->user->nip ?? '-' }}</dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Jabatan</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->user->jabatan ?? '-' }}</dd>
                        </div>
                        @endif
                        @if($transportRequest->jenis === 'ambulance')
                        <div class="border-top border-slate-100 pt-2 mt-1">
                            <div class="text-xxs fw-600 text-slate-500 text-uppercase tracking-wider mb-2">Data Pasien</div>
                            <div class="d-flex gap-2 mb-2">
                                <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Nama Pasien</dt>
                                <dd class="text-slate-800 fw-500 mb-0">{{ $transportRequest->pasien_nama ?? '-' }}</dd>
                            </div>
                            <div class="d-flex gap-2 mb-2">
                                <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">No. RM</dt>
                                <dd class="text-slate-700 font-mono mb-0">{{ $transportRequest->pasien_no_rm ?? '-' }}</dd>
                            </div>
                            <div class="d-flex gap-2 mb-2">
                                <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Alamat Pasien</dt>
                                <dd class="text-slate-700 mb-0">{{ $transportRequest->alamat_pasien ?? '-' }}</dd>
                            </div>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Kendaraan & Driver --}}
            <div class="col-12 col-lg-6">
                <div class="sp-card overflow-hidden h-100">
                    <div class="sp-card-header">
                        <h2 class="text-xs fw-600 text-slate-800 mb-0">Kendaraan &amp; Driver</h2>
                    </div>
                    <dl class="px-3 py-2 text-xs mb-0">
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Unit Kendaraan</dt>
                            <dd class="text-slate-800 fw-500 text-capitalize mb-0">
                                {{ $transportRequest->unit_mobil ? str_replace('_', ' ', $transportRequest->unit_mobil) : '-' }}
                            </dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Plat Nomor</dt>
                            <dd class="text-slate-700 font-mono mb-0">{{ $transportRequest->plat_nomor ?? '-' }}</dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Driver</dt>
                            <dd class="text-slate-800 fw-500 mb-0">{{ $transportRequest->driver->name ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Data Perjalanan --}}
            <div class="col-12 col-lg-6">
                <div class="sp-card overflow-hidden h-100">
                    <div class="sp-card-header">
                        <h2 class="text-xs fw-600 text-slate-800 mb-0">Data Perjalanan</h2>
                    </div>
                    <dl class="px-3 py-2 text-xs mb-0">
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">KM Awal</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->km_awal ? number_format($transportRequest->km_awal, 0, ',', '.') . ' km' : '-' }}</dd>
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">KM Akhir</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->km_akhir ? number_format($transportRequest->km_akhir, 0, ',', '.') . ' km' : '-' }}</dd>
                        </div>
                        @if($transportRequest->km_awal && $transportRequest->km_akhir)
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Jarak Tempuh</dt>
                            <dd class="fw-600 text-emerald-700 mb-0">{{ number_format($transportRequest->km_akhir - $transportRequest->km_awal, 0, ',', '.') }} km</dd>
                        </div>
                        @endif
                        <div class="d-flex gap-2 mb-2">
                            <dt class="text-slate-500 flex-shrink-0" style="width:7rem;">Jam Tiba</dt>
                            <dd class="text-slate-700 mb-0">{{ $transportRequest->jam_kedatangan ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        {{-- Tanda Tangan --}}
        <div class="mt-3 sp-card overflow-hidden">
            <div class="sp-card-header">
                <h2 class="text-xs fw-600 text-slate-800 mb-0">Riwayat Tanda Tangan</h2>
            </div>
            <div class="px-3 py-2">
                <div class="row g-3 text-xs">
                    @foreach([
                        ['label' => 'Pemohon',     'signed' => $transportRequest->signature_pemohon,     'at' => $transportRequest->signature_pemohon_at,     'name' => $transportRequest->user->full_name ?? $transportRequest->pemohon_nama],
                        ['label' => 'Pengelola 1', 'signed' => $transportRequest->signature_pengelola_1, 'at' => $transportRequest->signature_pengelola_1_at, 'name' => $transportRequest->signature_pengelola_1_name],
                        ['label' => 'Driver',       'signed' => $transportRequest->signature_driver,      'at' => $transportRequest->signature_driver_at,      'name' => $transportRequest->driver->name ?? null],
                        ['label' => 'Pengelola 2', 'signed' => $transportRequest->signature_pengelola_2, 'at' => $transportRequest->signature_pengelola_2_at, 'name' => $transportRequest->signature_pengelola_2_name],
                    ] as $ttd)
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 rounded {{ $ttd['signed'] ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }}">
                            <div class="text-xxs fw-600 {{ $ttd['signed'] ? 'text-emerald-700' : 'text-slate-400' }} mb-1">
                                {{ $ttd['label'] }}
                            </div>
                            @if($ttd['signed'])
                                <svg style="width:1rem;height:1rem;" class="text-emerald-600 mx-auto mb-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                @if($ttd['name'])
                                    <div class="text-xxs text-slate-700 fw-500 truncate">{{ $ttd['name'] }}</div>
                                @endif
                                @if($ttd['at'])
                                    <div class="text-xxs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($ttd['at'])->format('d/m/Y H:i') }}</div>
                                @endif
                            @else
                                <svg style="width:1rem;height:1rem;" class="text-slate-300 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-xxs text-slate-400 mt-1">Belum TTD</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
