<x-app-layout title="Status Pengajuan — SIPETRANS">
    @php
        $statusCfg = match ($item->status) {
            'diajukan' => ['badge-amber', 'bg-amber-50', 'border-amber-300', 'bg-amber-400', 'Diajukan'],
            'diproses' => ['badge-blue', 'bg-blue-50', 'border-blue-300', 'bg-blue-500', 'Disetujui'],
            'digunakan' => ['badge-cyan', 'bg-cyan-50', 'border-cyan-300', 'bg-cyan-500', 'Digunakan'],
            'selesai' => ['badge-emerald', 'bg-emerald-50', 'border-emerald-300', 'bg-emerald-500', 'Selesai'],
            'tidak_disetujui' => ['badge-red', 'bg-red-50', 'border-red-300', 'bg-red-500', 'Tidak Disetujui'],
            default => ['badge-slate', 'bg-slate-50', 'border-slate-300', 'bg-slate-400', ucfirst($item->status)],
        };
        $steps = ['diajukan', 'diproses', 'digunakan', 'selesai'];
        $currentStep = array_search($item->status, $steps);
        $isRejected = $item->status === 'tidak_disetujui';
    @endphp

    <div class="mx-auto px-3 pt-3 pb-4" style="max-width:48rem;">

        {{-- ── TOP BAR ── --}}
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="text-sm fw-bold text-slate-800 mb-0">Detail Pengajuan</h1>
                    <span
                        class="badge {{ $statusCfg[0] }} d-inline-flex align-items-center gap-1 rounded-pill text-xxs fw-bold">
                        <span class="rounded-circle"
                            style="width:.375rem; height:.375rem; display:inline-block; background:currentColor; opacity:.7;"></span>
                        {{ $statusCfg[4] }}
                    </span>
                    @if($item->prioritas === 'segera')
                        <span class="badge bg-danger text-white rounded-pill text-xxs fw-bold">⚡ CITO</span>
                    @endif
                </div>
                <p class="text-xxs text-slate-400 mt-1 mb-0 font-mono">{{ $item->nomor_pengajuan }}</p>
            </div>
            <a href="{{ route('pengajuan.index') }}"
                class="d-inline-flex align-items-center gap-1 rounded border border-slate-300 bg-white px-3 py-2 text-xs fw-600 text-slate-600 shrink-0">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Riwayat
            </a>
        </div>

        {{-- ── STEPPER / REJECTED BANNER ── --}}
        @if(!$isRejected)
            <div class="sp-card px-3 py-2 mb-3">
                <div class="d-flex align-items-center position-relative">
                    <div class="position-absolute bg-slate-200"
                        style="left:.75rem; right:.75rem; top:.75rem; height:1px; z-index:0;"></div>
                    @foreach(['diajukan' => 'Diajukan', 'diproses' => 'Disetujui', 'digunakan' => 'Digunakan', 'selesai' => 'Selesai'] as $step => $stepLabel)
                        @php
                            $idx = array_search($step, $steps);
                            $done = $currentStep !== false && $idx <= $currentStep;
                            $active = $step === $item->status;
                        @endphp
                        <div class="position-relative d-flex flex-column align-items-center flex-grow-1" style="z-index:1;">
                            <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold border-2" style="width:1.5rem; height:1.5rem; font-size:.5625rem;
                                                {{ $done ? 'background-color:#059669; border-color:#059669; color:#fff;' : 'background-color:#fff; border-color:#cbd5e1; color:#94a3b8;' }}
                                                {{ $active ? 'box-shadow:0 0 0 3px rgba(5,150,105,.25);' : '' }}
                                                border-width:2px; border-style:solid;">
                                @if($done && !$active)
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            <span
                                class="text-xxs fw-500 mt-1 whitespace-nowrap {{ $done ? 'text-emerald-700' : 'text-slate-400' }}">{{ $stepLabel }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="d-flex align-items-start gap-2 bg-red-50 border border-red-200 rounded px-3 py-2 mb-3">
                <svg class="text-red-500 shrink-0 mt-1" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-xs fw-600 text-red-700 mb-0">Tidak Disetujui
                    @if($item->rejection_reason)
                        <span class="fw-normal text-red-600"> — {{ $item->rejection_reason }}</span>
                    @endif
                </p>
            </div>
        @endif

        {{-- ── SINGLE COLUMN STACK ── --}}
        <div class="d-flex flex-column gap-3">

            {{-- TOP: Info Pengajuan + Pasien --}}
            <div class="d-flex flex-column gap-3">

                {{-- Info Pengajuan --}}
                <div class="sp-card overflow-hidden">
                    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-slate-50 border-bottom border-slate-200">
                        <svg class="text-emerald-600" width="12" height="12" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h2 class="text-xxs fw-bold text-slate-600 text-uppercase tracking-wide mb-0">Informasi
                            Pengajuan</h2>
                    </div>
                    <div class="px-3 py-1">
                        <table class="w-100 text-xs">
                            <tbody class="divide-y divide-slate-100">
                                <tr>
                                    <td class="py-2 pe-3 text-slate-400 whitespace-nowrap" style="width:5rem;">Jenis
                                    </td>
                                    <td class="py-2">
                                        <span
                                            class="badge {{ $item->jenis === 'ambulance' ? 'badge-red' : 'badge-amber' }} d-inline-flex align-items-center rounded-pill text-xxs fw-bold">
                                            {{ ucfirst($item->jenis) }}
                                        </span>
                                        @if($item->keperluan)
                                            <span class="text-slate-500 ms-1">· {{ ucfirst($item->keperluan) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 pe-3 text-slate-400 whitespace-nowrap">Jadwal</td>
                                    <td class="py-2 fw-500 text-slate-800" style="font-size:.6875rem;">
                                        {{ $item->tanggal?->format('d M Y') }}, {{ substr($item->jam, 0, 5) }}
                                        @if($item->tanggal_sampai && $item->jam_sampai)
                                            <span class="text-slate-400 fw-normal d-block">→
                                                {{ $item->tanggal_sampai->format('d M Y') }},
                                                {{ substr($item->jam_sampai, 0, 5) }}</span>
                                        @else
                                            <span class="text-slate-400 fw-normal"> → Selesai</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($item->alamat_tujuan || $item->alamat_asal)
                                    <tr>
                                        <td class="py-2 pe-3 text-slate-400 whitespace-nowrap">Rute</td>
                                        <td class="py-2 text-slate-800" style="font-size:.6875rem;">
                                            <span class="text-slate-500">{{ $item->alamat_asal ?: 'RS Azra' }}</span>
                                            <span class="text-slate-400 mx-1">→</span>
                                            <span class="fw-500">{{ $item->alamat_tujuan ?: '-' }}</span>
                                        </td>
                                    </tr>
                                @endif
                                @if($item->jumlah_penumpang)
                                    <tr>
                                        <td class="py-2 pe-3 text-slate-400 whitespace-nowrap">Penumpang</td>
                                        <td class="py-2 fw-500 text-slate-800">{{ $item->jumlah_penumpang }} orang</td>
                                    </tr>
                                @endif
                                @if($item->keterangan)
                                    <tr>
                                        <td class="py-2 pe-3 text-slate-400 whitespace-nowrap">Keterangan</td>
                                        <td class="py-2 text-slate-700">{{ $item->keterangan }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="py-2 pe-3 text-slate-400 whitespace-nowrap">Dibuat</td>
                                    <td class="py-2 text-slate-400 text-xxs">
                                        {{ $item->created_at->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Data Pasien (ambulance) --}}
                @if($item->jenis === 'ambulance' && $item->pasien_nama)
                    <div class="sp-card overflow-hidden" style="border-color:#fecdd3;">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom"
                            style="background-color:#fff1f2; border-color:#fecdd3;">
                            <svg class="text-red-600" width="12" height="12" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <h2 class="text-xxs fw-bold text-uppercase tracking-wide mb-0" style="color:#be123c;">Data
                                Pasien</h2>
                        </div>
                        <div class="px-3 py-1">
                            <table class="w-100 text-xs">
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="py-2 pe-3 text-slate-400 whitespace-nowrap" style="width:5rem;">Nama</td>
                                        <td class="py-2 fw-500 text-slate-800">{{ $item->pasien_nama }}</td>
                                    </tr>
                                    @if($item->pasien_no_rm)
                                        <tr>
                                            <td class="py-2 pe-3 text-slate-400">No. RM</td>
                                            <td class="py-2 font-mono text-slate-800">{{ $item->pasien_no_rm }}</td>
                                        </tr>
                                    @endif
                                    @if($item->alamat_pasien)
                                        <tr>
                                            <td class="py-2 pe-3 text-slate-400">Alamat</td>
                                            <td class="py-2 text-slate-800">{{ $item->alamat_pasien }}</td>
                                        </tr>
                                    @endif
                                    @if($item->pendamping_nama)
                                        <tr>
                                            <td class="py-2 pe-3 text-slate-400">Pendamping</td>
                                            <td class="py-2 text-slate-800">{{ $item->pendamping_nama }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>{{-- end top --}}

            {{-- BOTTOM: Kendaraan + Aksi --}}
            <div class="d-flex flex-column gap-3">

                {{-- Kendaraan & Perjalanan --}}
                @if(in_array($item->status, ['digunakan', 'selesai']) && $item->unit_mobil)
                    <div class="sp-card overflow-hidden">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 bg-slate-50 border-bottom border-slate-200">
                            <svg class="text-emerald-600" width="12" height="12" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4h13l2 4H3V4z" />
                            </svg>
                            <h2 class="text-xxs fw-bold text-slate-600 text-uppercase tracking-wide mb-0">Kendaraan &amp;
                                Perjalanan</h2>
                        </div>
                        <div class="px-3 py-2">
                            <div class="row g-2 text-xs">
                                <div class="col-6">
                                    <div class="bg-slate-50 rounded p-2 border border-slate-200">
                                        <p class="text-xxs text-slate-400 fw-500 mb-1">Unit</p>
                                        <p class="fw-bold text-slate-800 mb-0" style="font-size:.6875rem;">
                                            {{ $item->unit_mobil }}
                                        </p>
                                        @if($item->plat_nomor)
                                        <p class="text-xxs text-slate-500 font-mono mb-0">{{ $item->plat_nomor }}</p>@endif
                                    </div>
                                </div>
                                @if($item->driver)
                                    <div class="col-6">
                                        <div class="bg-slate-50 rounded p-2 border border-slate-200">
                                            <p class="text-xxs text-slate-400 fw-500 mb-1">Pengemudi</p>
                                            <p class="fw-bold text-slate-800 mb-0" style="font-size:.6875rem;">
                                                {{ $item->driver->name }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if($item->km_awal)
                                    <div class="col-6">
                                        <div class="bg-emerald-50 rounded p-2 border border-emerald-200">
                                            <p class="text-xxs text-emerald-600 fw-500 mb-1">KM Berangkat</p>
                                            <p class="fw-bold text-emerald-800 mb-0" style="font-size:.6875rem;">
                                                {{ number_format($item->km_awal, 0, ',', '.') }} km
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if($item->km_akhir)
                                    <div class="col-6">
                                        <div class="bg-emerald-50 rounded p-2 border border-emerald-200">
                                            <p class="text-xxs text-emerald-600 fw-500 mb-1">KM Tiba</p>
                                            <p class="fw-bold text-emerald-800 mb-0" style="font-size:.6875rem;">
                                                {{ number_format($item->km_akhir, 0, ',', '.') }} km
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if($item->km_awal && $item->km_akhir)
                                    <div class="col-12">
                                        <div
                                            class="bg-emerald-50 rounded p-2 border border-emerald-200 d-flex align-items-center gap-2">
                                            <svg class="text-emerald-600 shrink-0" width="14" height="14" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                            </svg>
                                            <div>
                                                <p class="text-xxs text-emerald-600 fw-500 mb-0">Total Jarak</p>
                                                <p class="fw-bold text-emerald-800 mb-0" style="font-size:.6875rem;">
                                                    {{ number_format($item->km_akhir - $item->km_awal, 0, ',', '.') }} km
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($item->jam_kedatangan)
                                    <div class="col-12">
                                        <div
                                            class="bg-slate-50 rounded p-2 border border-slate-200 d-flex align-items-center gap-2">
                                            <svg class="text-slate-500 shrink-0" width="14" height="14" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-xxs text-slate-400 fw-500 mb-0">Jam Kedatangan</p>
                                                <p class="fw-bold text-slate-800 mb-0" style="font-size:.6875rem;">
                                                    {{ $item->jam_kedatangan }} WIB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Aksi --}}
                <div class="d-flex flex-row justify-content-end gap-2">
                    @if($item->status !== 'tidak_disetujui')
                        <a href="{{ route('pengajuan.print', $item) }}" target="_blank"
                            class="d-inline-flex justify-content-center align-items-center gap-2 px-4 py-2 rounded text-xs fw-600 shadow-sm"
                            style="background-color:#059669; color:white !important;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Surat
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}"
                        class="d-inline-flex justify-content-center align-items-center px-4 py-2 rounded text-xs fw-600 shadow-sm"
                        style="background-color:#059669; color:white !important;">
                        Buat Pengajuan Baru
                    </a>
                </div>

            </div>{{-- end bottom --}}

        </div>{{-- end stack --}}

    </div>
</x-app-layout>