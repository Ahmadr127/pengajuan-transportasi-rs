<x-app-layout title="Detail Pengajuan — SIPETRANS">
    @php
        $colors = [
            'diajukan' => ['badge' => 'badge-amber', 'dot' => 'bg-amber-400'],
            'diproses' => ['badge' => 'badge-blue', 'dot' => 'bg-blue-500'],
            'digunakan' => ['badge' => 'badge-cyan', 'dot' => 'bg-cyan-500'],
            'selesai' => ['badge' => 'badge-emerald', 'dot' => 'bg-emerald-500'],
            'tidak_disetujui' => ['badge' => 'badge-red', 'dot' => 'bg-red-500'],
        ];
        $sc = $colors[$transportRequest->status] ?? $colors['diajukan'];
        $label = match ($transportRequest->status) {
            'diproses' => 'Disetujui',
            'digunakan' => 'Digunakan',
            'tidak_disetujui' => 'Tidak Disetujui',
            default => ucfirst($transportRequest->status)
        };
        $steps = ['diajukan', 'diproses', 'digunakan', 'selesai'];
        $currentStep = array_search($transportRequest->status, $steps);
        $isRejected = $transportRequest->status === 'tidak_disetujui';
    @endphp

    <div class="container-fluid px-3 px-sm-4 pt-4 pb-5">
        <div style="max-width:64rem;margin:0 auto;">

            {{-- TOP BAR --}}
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h1 class="text-sm fw-bold text-slate-800 mb-0">Detail Pengajuan</h1>
                        <span class="badge status-badge {{ $sc['badge'] }} d-inline-flex align-items-center gap-1">
                            <span class="rounded-circle {{ $sc['dot'] }}"
                                style="width:.375rem;height:.375rem;display:inline-block;"></span>
                            {{ $label }}
                        </span>
                        @if($transportRequest->prioritas === 'segera')
                            <span class="badge status-badge badge-red">&#9889; CITO</span>
                        @endif
                    </div>
                    <p class="text-xxs text-slate-400 mt-1 font-mono mb-0">{{ $transportRequest->nomor_pengajuan }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($transportRequest->status !== 'tidak_disetujui')
                        <a href="{{ route('admin.transport.print', $transportRequest) }}" target="_blank"
                            class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Surat
                        </a>
                    @endif
                    <a href="{{ route('admin.transport.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 text-xs fw-600">
                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
                <div class="alert alert-sp-success d-flex align-items-center gap-2 p-3 rounded mb-3 text-xs fw-500">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-sp-danger p-3 rounded mb-3 text-xs">
                    <div class="fw-600 mb-1">Periksa kembali:</div>
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- PROGRESS STEPPER --}}
            @if(!$isRejected)
                <div class="sp-card px-3 py-2 mb-3">
                    <div class="d-flex align-items-center position-relative">
                        <div class="position-absolute start-0 end-0"
                            style="top:.75rem;height:1px;background:#e2e8f0;z-index:0;margin:0 .75rem;"></div>
                        @foreach(['diajukan' => 'Diajukan', 'diproses' => 'Disetujui', 'digunakan' => 'Digunakan', 'selesai' => 'Selesai'] as $step => $stepLabel)
                            @php
                                $stepIdx = array_search($step, $steps);
                                $done = $currentStep !== false && $stepIdx <= $currentStep;
                                $active = $step === $transportRequest->status;
                            @endphp
                            <div class="position-relative d-flex flex-column align-items-center flex-grow-1" style="z-index:1;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width:1.5rem;height:1.5rem;font-size:.5625rem;border:2px solid;
                             {{ $done ? 'background:#007774;border-color:#007774;color:#fff;' : 'background:#fff;border-color:#cbd5e1;color:#94a3b8;' }}
                             {{ $active ? 'box-shadow:0 0 0 3px rgba(0,119,116,.25);' : '' }}">
                                    @if($done && !$active)
                                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        {{ $stepIdx + 1 }}
                                    @endif
                                </div>
                                <span
                                    class="text-xxs fw-500 mt-1 whitespace-nowrap {{ $done ? 'text-teal-700' : 'text-slate-400' }}">{{ $stepLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center gap-2 bg-red-50 border border-red-200 rounded px-3 py-2 mb-3">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" class="text-red-500" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs fw-600 text-red-700 mb-0">Tidak Disetujui
                        @if($transportRequest->rejection_reason)
                            <span class="fw-normal text-red-600"> &mdash; {{ $transportRequest->rejection_reason }}</span>
                        @endif
                    </p>
                </div>
            @endif

            {{-- MAIN GRID --}}
            <div class="row g-4">

                {{-- LEFT COLUMN --}}
                <div class="col-12 col-lg-8">

                    {{-- Card: Informasi Pengajuan --}}
                    <div class="sp-card overflow-hidden mb-3">
                        <div class="sp-card-header d-flex align-items-center gap-2">
                            <svg style="width:.75rem;height:.75rem;" class="text-teal-700" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h2 class="text-xxs fw-bold text-slate-600 text-uppercase tracking-wide mb-0">Informasi
                                Pengajuan</h2>
                        </div>
                        <div class="px-3 py-2">
                            <table class="w-100 text-xs">
                                <tbody class="divide-y divide-slate-100">
                                    <tr>
                                        <td class="py-1 pe-3 text-slate-400 whitespace-nowrap" style="width:7rem;">
                                            Pemohon</td>
                                        <td class="py-1 fw-500 text-slate-800">
                                            {{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}
                                            <span class="text-slate-400 fw-normal"> &middot;
                                                {{ $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit }}</span>
                                            @if($transportRequest->user->nip ?? null)
                                                <span class="text-slate-400 font-mono fw-normal"> &middot;
                                                    {{ $transportRequest->user->nip }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Jenis</td>
                                        <td class="py-1">
                                            <span
                                                class="badge status-badge {{ $transportRequest->jenis === 'ambulance' ? 'bg-red-100 text-red-700' : 'bg-teal-50 text-teal-700' }}">
                                                {{ ucfirst($transportRequest->jenis) }}
                                            </span>
                                            @if($transportRequest->jenis === 'ambulance' && $transportRequest->keperluan)
                                                <span
                                                    class="text-slate-500 ms-1">({{ ucfirst($transportRequest->keperluan) }})</span>
                                            @endif
                                            @if($transportRequest->jenis === 'umum' && $transportRequest->jumlah_penumpang)
                                                <span class="text-slate-500 ms-1">&middot;
                                                    {{ $transportRequest->jumlah_penumpang }} penumpang</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Jadwal</td>
                                        <td class="py-1 fw-500 text-slate-800">
                                            {{ $transportRequest->tanggal->format('d M Y') }},
                                            {{ substr($transportRequest->jam, 0, 5) }}
                                            @if($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
                                                <span class="text-slate-400">&rarr;</span>
                                                {{ $transportRequest->tanggal_sampai->format('d M Y') }},
                                                {{ substr($transportRequest->jam_sampai, 0, 5) }}
                                            @else
                                                <span class="text-slate-400">&rarr; Sampai Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Tujuan</td>
                                        <td class="py-1 fw-500 text-slate-800">
                                            {{ $transportRequest->alamat_tujuan ?? '-' }}
                                            @if($transportRequest->alamat_asal)
                                                <span class="text-slate-400 fw-normal"> (dari:
                                                    {{ $transportRequest->alamat_asal }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($transportRequest->keperluan && $transportRequest->jenis === 'umum')
                                        <tr>
                                            <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Keperluan</td>
                                            <td class="py-1 text-slate-700">{{ $transportRequest->keperluan }}</td>
                                        </tr>
                                    @endif
                                    @if($transportRequest->keterangan)
                                        <tr>
                                            <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Keterangan</td>
                                            <td class="py-1 text-slate-700">{{ $transportRequest->keterangan }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Dibuat</td>
                                        <td class="py-1 text-slate-400">
                                            {{ $transportRequest->created_at->format('d M Y, H:i') }} WIB</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Card: Data Pasien (ambulance only) --}}
                    @if($transportRequest->jenis === 'ambulance')
                        <div class="sp-card overflow-hidden border-red-200 mb-3">
                            <div
                                class="sp-card-header d-flex align-items-center gap-2 bg-red-50 border-bottom border-red-200">
                                <svg style="width:.75rem;height:.75rem;" class="text-red-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h2 class="text-xxs fw-bold text-red-700 text-uppercase tracking-wide mb-0">Data Pasien</h2>
                            </div>
                            <div class="px-3 py-2">
                                <table class="w-100 text-xs">
                                    <tbody class="divide-y divide-slate-100">
                                        <tr>
                                            <td class="py-1 pe-3 text-slate-400 whitespace-nowrap" style="width:7rem;">Nama
                                            </td>
                                            <td class="py-1 fw-500 text-slate-800">
                                                {{ $transportRequest->pasien_nama ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">No. RM</td>
                                            <td class="py-1 font-mono text-slate-800">
                                                {{ $transportRequest->pasien_no_rm ?? '-' }}</td>
                                        </tr>
                                        @if($transportRequest->ruangan)
                                            <tr>
                                                <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Ruangan</td>
                                                <td class="py-1 text-slate-800">{{ $transportRequest->ruangan }}</td>
                                            </tr>
                                        @endif
                                        @if($transportRequest->pendamping_nama)
                                            <tr>
                                                <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Pendamping</td>
                                                <td class="py-1 text-slate-800">{{ $transportRequest->pendamping_nama }}</td>
                                            </tr>
                                        @endif
                                        @if($transportRequest->alamat_pasien)
                                            <tr>
                                                <td class="py-1 pe-3 text-slate-400 whitespace-nowrap">Alamat</td>
                                                <td class="py-1 text-slate-800">{{ $transportRequest->alamat_pasien }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Card: Kendaraan & Perjalanan --}}
                    @if(in_array($transportRequest->status, ['digunakan', 'selesai']))
                        <div class="sp-card overflow-hidden mb-3">
                            <div class="sp-card-header d-flex align-items-center gap-2">
                                <svg style="width:.875rem;height:.875rem;" class="text-teal-700" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4h13l2 4H3V4z" />
                                </svg>
                                <h2 class="text-xxs fw-bold text-slate-700 text-uppercase tracking-wide mb-0">Kendaraan
                                    &amp; Perjalanan</h2>
                            </div>
                            <div class="px-3 py-3">
                                <div class="row g-2 text-xs">
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-slate-50 rounded p-2 border border-slate-200">
                                            <p class="text-xxs text-slate-400 fw-500 mb-1">Unit</p>
                                            <p class="fw-bold text-slate-800 mb-0" style="font-size:.6875rem;">
                                                {{ $transportRequest->unit_mobil ?? '-' }}</p>
                                            @if($transportRequest->plat_nomor)
                                                <p class="text-xxs text-slate-500 font-mono mb-0">
                                                    {{ $transportRequest->plat_nomor }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-slate-50 rounded p-2 border border-slate-200">
                                            <p class="text-xxs text-slate-400 fw-500 mb-1">Pengemudi</p>
                                            <p class="fw-bold text-slate-800 mb-0" style="font-size:.6875rem;">
                                                {{ $transportRequest->driver->name ?? '-' }}</p>
                                            @if($transportRequest->driver?->phone)
                                                <p class="text-xxs text-slate-500 mb-0">{{ $transportRequest->driver->phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-teal-50 rounded p-2 border border-cyan-200">
                                            <p class="text-xxs text-teal-600 fw-500 mb-1">KM Berangkat</p>
                                            <p class="fw-bold text-teal-800 mb-0" style="font-size:.6875rem;">
                                                {{ $transportRequest->km_awal ? number_format($transportRequest->km_awal, 0, ',', '.') : '-' }}
                                                km</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="bg-teal-50 rounded p-2 border border-cyan-200">
                                            <p class="text-xxs text-teal-600 fw-500 mb-1">KM Tiba</p>
                                            <p class="fw-bold text-teal-800 mb-0" style="font-size:.6875rem;">
                                                {{ $transportRequest->km_akhir ? number_format($transportRequest->km_akhir, 0, ',', '.') : '-' }}
                                                km</p>
                                        </div>
                                    </div>
                                    @if($transportRequest->km_awal && $transportRequest->km_akhir)
                                        <div class="col-12 col-sm-6">
                                            <div
                                                class="bg-emerald-50 rounded p-2 border border-emerald-200 d-flex align-items-center gap-2">
                                                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;"
                                                    class="text-emerald-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                                </svg>
                                                <div>
                                                    <p class="text-xxs text-emerald-600 fw-500 mb-0">Total Jarak</p>
                                                    <p class="fw-bold text-emerald-800 mb-0">
                                                        {{ number_format($transportRequest->km_akhir - $transportRequest->km_awal, 0, ',', '.') }}
                                                        km</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($transportRequest->jam_kedatangan)
                                        <div class="col-12 col-sm-6">
                                            <div
                                                class="bg-slate-50 rounded p-2 border border-slate-200 d-flex align-items-center gap-2">
                                                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" class="text-slate-500"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>
                                                    <p class="text-xxs text-slate-400 fw-500 mb-0">Jam Kedatangan</p>
                                                    <p class="fw-bold text-slate-800 mb-0">
                                                        {{ $transportRequest->jam_kedatangan }} WIB</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($transportRequest->biaya_tol)
                                        <div class="col-12 col-sm-6">
                                            <div
                                                class="bg-amber-50 rounded p-2 border border-amber-200 d-flex align-items-center gap-2">
                                                <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" class="text-amber-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <div>
                                                    <p class="text-xxs text-amber-600 fw-500 mb-0">Biaya E-Tol</p>
                                                    <p class="fw-bold text-amber-800 mb-0">Rp
                                                        {{ number_format($transportRequest->biaya_tol, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>{{-- end left column --}}

                {{-- RIGHT COLUMN: Admin Action --}}
                <div class="col-12 col-lg-4">
                    <div class="sp-card overflow-hidden sticky-top"
                        x-data="{ currentStatus: '{{ $transportRequest->status }}', savedStatus: '{{ $transportRequest->status }}', editOpen: false }">
                        <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom"
                            style="background:#007774;">
                            <svg style="width:.875rem;height:.875rem;color:#99f6e4;" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h2 class="text-xxs fw-bold text-white text-uppercase tracking-wide mb-0">Eksekusi Admin
                            </h2>
                        </div>

                        @php $isBlocked = false; @endphp

                        <form method="POST" action="{{ route('admin.transport.update', $transportRequest) }}"
                            class="px-3 py-3 text-xs" id="mainFormWrapper">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label
                                    class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Ubah
                                    Status</label>
                                <select name="status" x-model="currentStatus" class="form-select form-select-sm">
                                    @if($transportRequest->status === 'diajukan')
                                        <option value="diajukan">Diajukan (Menunggu)</option>
                                        @if($unitAvailable || ($transportRequest->user && $transportRequest->user->isPriority()))
                                            <option value="diproses">&#10003; Setujui</option>
                                        @endif
                                        <option value="tidak_disetujui">&#10007; Tolak</option>
                                    @elseif($transportRequest->status === 'diproses')
                                        <option value="diproses">Disetujui</option>
                                        <option value="digunakan">&rarr; Tandai Digunakan</option>
                                    @elseif($transportRequest->status === 'digunakan')
                                        <option value="digunakan">Digunakan</option>
                                        <option value="selesai">&#10003; Tandai Selesai</option>
                                        <option value="tidak_disetujui">&#10007; Batalkan</option>
                                    @elseif($transportRequest->status === 'selesai')
                                        <option value="selesai">Selesai</option>
                                    @elseif($transportRequest->status === 'tidak_disetujui')
                                        <option value="tidak_disetujui">Tidak Disetujui</option>
                                    @endif
                                </select>
                                @if($transportRequest->status === 'diajukan' && !$unitAvailable && !($transportRequest->user && $transportRequest->user->isPriority()))
                                    <div
                                        class="mt-2 d-flex align-items-start gap-2 rounded bg-red-50 border border-red-200 px-2 py-2">
                                        <svg style="width:.875rem;height:.875rem;flex-shrink:0;margin-top:.1rem;"
                                            class="text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-xxs text-red-700 mb-0">Semua unit penuh di waktu ini. Hanya bisa
                                            ditolak.</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="currentStatus === 'tidak_disetujui' && savedStatus === 'diajukan'"
                                class="mb-3">
                                <label
                                    class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Alasan
                                    Penolakan <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" rows="3" class="form-control form-control-sm"
                                    placeholder="Tuliskan alasan...">{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')<p class="text-xxs text-danger mt-1 mb-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="currentStatus === 'digunakan' && '{{ $transportRequest->status }}' === 'diproses'"
                                class="mb-3">
                                <div class="mb-2">
                                    <label
                                        class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Unit
                                        Kendaraan <span class="text-danger">*</span></label>
                                    <select name="unit_mobil" id="unit_mobil" class="form-select form-select-sm">
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->name }}" data-plate="{{ $vehicle->plate_number }}"
                                                data-last-km="{{ $vehicle->last_km ?? 0 }}"
                                                @selected(old('unit_mobil') == $vehicle->name)>
                                                {{ $vehicle->name }} ({{ $vehicle->plate_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="plat_nomor" id="plat_nomor"
                                        value="{{ old('plat_nomor', $transportRequest->plat_nomor) }}">
                                </div>
                                <div class="mb-2">
                                    <label
                                        class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Pengemudi
                                        <span class="text-danger">*</span></label>
                                    <select name="driver_id" class="form-select form-select-sm">
                                        <option value="">-- Pilih Driver --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" @selected(old('driver_id', $transportRequest->driver_id) == $driver->id)>
                                                {{ $driver->name }}@if($driver->phone) &middot; {{ $driver->phone }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label
                                        class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">KM
                                        Keberangkatan <span class="text-danger">*</span></label>
                                    <input type="number" name="km_awal" id="km_awal"
                                        value="{{ old('km_awal', $transportRequest->km_awal) }}"
                                        class="form-control form-control-sm" placeholder="Masukkan KM" min="0">
                                    <p id="km_awal_hint" class="text-xxs text-amber-600 mt-1 mb-0 d-none"></p>
                                    <p id="km_awal_error" class="text-xxs text-danger mt-1 mb-0 d-none"></p>
                                </div>
                            </div>

                            <div x-show="currentStatus === 'selesai' && '{{ $transportRequest->status }}' === 'digunakan'"
                                class="mb-3">
                                <div class="mb-2">
                                    <label
                                        class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">KM
                                        Tiba <span class="text-danger">*</span></label>
                                    <input type="number" name="km_akhir" id="km_akhir"
                                        value="{{ old('km_akhir', $transportRequest->km_akhir) }}"
                                        class="form-control form-control-sm" placeholder="Masukkan KM" min="0">
                                </div>
                                <div class="mb-2">
                                    <label
                                        class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Jam
                                        Kedatangan <span class="text-danger">*</span></label>
                                    <input type="text" name="jam_kedatangan" id="jam_kedatangan"
                                        value="{{ old('jam_kedatangan', $transportRequest->jam_kedatangan ?? now()->format('H:i')) }}"
                                        class="form-control form-control-sm" placeholder="00:00" maxlength="5"
                                        inputmode="numeric">
                                </div>
                            </div>

                            {{-- Alasan pembatalan (digunakan → tidak_disetujui) --}}
                            <div x-show="currentStatus === 'tidak_disetujui' && '{{ $transportRequest->status }}' === 'digunakan'"
                                class="mb-3" style="display:none;">
                                <label
                                    class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Alasan
                                    Pembatalan <span class="text-danger">*</span></label>
                                <textarea name="rejection_reason" rows="3" class="form-control form-control-sm"
                                    placeholder="Tuliskan alasan...">{{ old('rejection_reason', 'Dibatalkan') }}</textarea>
                                @error('rejection_reason')<p class="text-xxs text-danger mt-1 mb-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-show="'{{ $transportRequest->status }}' === 'digunakan' || '{{ $transportRequest->status }}' === 'selesai'"
                                class="bg-slate-50 rounded border border-slate-200 p-3 mb-3"
                                style="font-size:.6875rem;">
                                <p class="text-xxs fw-bold text-slate-600 text-uppercase tracking-wide mb-2">Data Terisi
                                </p>
                                @if($transportRequest->unit_mobil)
                                    <div class="d-flex justify-content-between mb-1"><span
                                            class="text-slate-500">Unit</span><span
                                            class="fw-600 text-slate-800">{{ $transportRequest->unit_mobil }}</span></div>
                                @endif
                                @if($transportRequest->driver_id)
                                    <div class="d-flex justify-content-between mb-1"><span
                                            class="text-slate-500">Driver</span><span
                                            class="fw-600 text-slate-800">{{ $transportRequest->driver->name ?? '-' }}</span>
                                    </div>
                                @endif
                                @if($transportRequest->km_awal)
                                    <div class="d-flex justify-content-between mb-1"><span class="text-slate-500">KM
                                            Awal</span><span
                                            class="fw-600 text-slate-800">{{ number_format($transportRequest->km_awal, 0, ',', '.') }}
                                            km</span></div>
                                @endif
                                @if($transportRequest->km_akhir)
                                    <div class="d-flex justify-content-between mb-1"><span class="text-slate-500">KM
                                            Akhir</span><span
                                            class="fw-600 text-slate-800">{{ number_format($transportRequest->km_akhir, 0, ',', '.') }}
                                            km</span></div>
                                @endif
                                @if($transportRequest->km_awal && $transportRequest->km_akhir)
                                    <div class="d-flex justify-content-between pt-1 border-top border-slate-200"><span
                                            class="text-slate-500">Total</span><span
                                            class="fw-bold text-emerald-700">{{ number_format($transportRequest->km_akhir - $transportRequest->km_awal, 0, ',', '.') }}
                                            km</span></div>
                                @endif
                                @if($transportRequest->jam_kedatangan)
                                    <div class="d-flex justify-content-between mt-1"><span class="text-slate-500">Jam
                                            Tiba</span><span
                                            class="fw-600 text-slate-800">{{ $transportRequest->jam_kedatangan }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($transportRequest->status === 'digunakan')
                                <div x-show="currentStatus !== 'selesai'" class="mb-3">
                                    <button type="button" @click="editOpen = !editOpen"
                                        class="btn btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-2 fw-600 transition"
                                        :class="editOpen ? 'btn-outline-secondary' : 'btn-sp-outline'">
                                        <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span x-text="editOpen ? 'Tutup Edit' : 'Edit Unit / Driver / KM'"></span>
                                    </button>
                                </div>
                            @endif

                            <div x-show="currentStatus !== savedStatus">
                                <button type="submit" @if($isBlocked) disabled @endif
                                    class="btn btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-2 fw-600 text-white transition {{ $isBlocked ? 'btn-secondary' : 'btn-sp-primary' }}">
                                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span
                                        x-text="currentStatus === 'selesai' ? 'Simpan &amp; Selesaikan' : 'Simpan Perubahan'"></span>
                                </button>
                            </div>
                        </form>

                        @if($transportRequest->status === 'digunakan')
                            <div x-show="editOpen && currentStatus !== 'selesai'" x-transition
                                class="px-3 pb-3 border-top border-slate-200 pt-3">
                                <form method="POST" action="{{ route('admin.transport.update', $transportRequest) }}"
                                    class="text-xs" id="editDigunakanFormEl">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="digunakan">
                                    <input type="hidden" name="_edit_digunakan" value="1">
                                    <p class="text-xxs fw-bold text-slate-600 text-uppercase tracking-wide mb-2">Edit Data
                                        Digunakan</p>
                                    <div class="mb-2">
                                        <label
                                            class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Unit
                                            Kendaraan</label>
                                        <select name="unit_mobil" id="unit_mobil_edit" class="form-select form-select-sm">
                                            <option value="">-- Pilih Unit --</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->name }}" data-plate="{{ $vehicle->plate_number }}"
                                                    @selected($transportRequest->unit_mobil == $vehicle->name)>
                                                    {{ $vehicle->name }} ({{ $vehicle->plate_number }})
                                                </option>
                                            @endforeach
                                            @if($transportRequest->unit_mobil && !$vehicles->contains('name', $transportRequest->unit_mobil))
                                                <option value="{{ $transportRequest->unit_mobil }}" selected>
                                                    {{ $transportRequest->unit_mobil }} &mdash; saat ini</option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="plat_nomor" id="plat_nomor_edit"
                                            value="{{ $transportRequest->plat_nomor }}">
                                    </div>
                                    <div class="mb-2">
                                        <label
                                            class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">Pengemudi</label>
                                        <select name="driver_id" class="form-select form-select-sm">
                                            <option value="">-- Pilih Driver --</option>
                                            @foreach($drivers as $driver)
                                                <option value="{{ $driver->id }}"
                                                    @selected($transportRequest->driver_id == $driver->id)>
                                                    {{ $driver->name }}@if($driver->phone) &middot; {{ $driver->phone }}@endif
                                                </option>
                                            @endforeach
                                            @if($transportRequest->driver_id && !$drivers->contains('id', $transportRequest->driver_id))
                                                <option value="{{ $transportRequest->driver_id }}" selected>
                                                    {{ $transportRequest->driver->name ?? '-' }} &mdash; saat ini</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label
                                            class="d-block text-xxs fw-600 text-slate-600 mb-1 text-uppercase tracking-wide">KM
                                            Keberangkatan</label>
                                        <input type="text" id="km_awal_edit_display"
                                            value="{{ $transportRequest->km_awal ? number_format($transportRequest->km_awal, 0, ',', '.') : '' }}"
                                            class="form-control form-control-sm" placeholder="Masukkan KM"
                                            inputmode="numeric" autocomplete="off">
                                        <input type="hidden" name="km_awal" id="km_awal_edit"
                                            value="{{ $transportRequest->km_awal }}">
                                    </div>
                                    <button type="submit" class="btn btn-sp-primary btn-sm w-100 fw-600">Simpan
                                        Perubahan</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>{{-- end right column --}}

            </div>{{-- end main grid --}}
        </div>{{-- end container inner --}}
    </div>{{-- end container-fluid --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitSel = document.getElementById('unit_mobil');
            const platIn = document.getElementById('plat_nomor');
            if (unitSel && platIn) {
                unitSel.addEventListener('change', function () {
                    platIn.value = this.options[this.selectedIndex].getAttribute('data-plate') || '';
                });
            }

            const jamEl = document.getElementById('jam_kedatangan');
            if (jamEl) {
                jamEl.addEventListener('input', function () {
                    let v = this.value.replace(/\D/g, '');
                    if (v.length > 2) v = v.slice(0, 2) + ':' + v.slice(2, 4);
                    this.value = v;
                });
                jamEl.addEventListener('keypress', e => { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
                jamEl.addEventListener('blur', function () {
                    const d = this.value.replace(/\D/g, '');
                    if (d.length === 4) {
                        let h = Math.min(parseInt(d.slice(0, 2)), 23);
                        let m = Math.min(parseInt(d.slice(2, 4)), 59);
                        this.value = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
                    }
                });
            }

            function validateKm() {
                const a = parseInt(document.getElementById('km_awal')?.value) || 0;
                const b = parseInt(document.getElementById('km_akhir')?.value) || 0;
                const el = document.getElementById('km_akhir');
                if (!el) return true;
                let alert = document.getElementById('km_akhir_alert');
                if (b > 0 && a > 0 && b <= a) {
                    el.classList.add('border-danger'); el.classList.remove('border-slate-300');
                    if (!alert) { alert = document.createElement('p'); alert.id = 'km_akhir_alert'; alert.className = 'mt-1 text-xxs text-danger'; el.parentNode.appendChild(alert); }
                    alert.textContent = 'KM tiba harus lebih besar dari KM berangkat (' + a.toLocaleString('id-ID') + ' km).';
                    return false;
                }
                el.classList.remove('border-danger'); el.classList.add('border-slate-300');
                if (alert) alert.remove();
                return true;
            }
            const kmAkhirEl = document.getElementById('km_akhir');
            if (kmAkhirEl) { kmAkhirEl.addEventListener('input', validateKm); kmAkhirEl.addEventListener('blur', validateKm); }

            const kmAwalEl = document.getElementById('km_awal');
            const kmHint = document.getElementById('km_awal_hint');
            const kmError = document.getElementById('km_awal_error');
            let minKm = 0;
            function updateKmHint() {
                if (!unitSel) return;
                const lastKm = parseInt(unitSel.options[unitSel.selectedIndex]?.dataset?.lastKm) || 0;
                minKm = lastKm;
                if (kmAwalEl) { kmAwalEl.min = lastKm; kmAwalEl.placeholder = lastKm > 0 ? 'Min. ' + lastKm.toLocaleString('id-ID') + ' km' : 'Masukkan KM'; }
                if (kmHint) { kmHint.textContent = lastKm > 0 ? 'KM terakhir: ' + lastKm.toLocaleString('id-ID') + ' km' : ''; kmHint.classList.toggle('d-none', !lastKm); }
                if (kmAwalEl?.value) validateKmAwal();
            }
            function validateKmAwal() {
                if (!kmAwalEl || !kmError) return true;
                const v = parseInt(kmAwalEl.value) || 0;
                if (minKm > 0 && v < minKm) {
                    kmAwalEl.classList.add('border-danger'); kmAwalEl.classList.remove('border-slate-300');
                    kmError.textContent = 'KM tidak boleh kurang dari ' + minKm.toLocaleString('id-ID') + ' km.';
                    kmError.classList.remove('d-none'); return false;
                }
                kmAwalEl.classList.remove('border-danger'); kmAwalEl.classList.add('border-slate-300');
                kmError.classList.add('d-none'); return true;
            }
            if (unitSel) { unitSel.addEventListener('change', updateKmHint); updateKmHint(); }
            if (kmAwalEl) { kmAwalEl.addEventListener('blur', validateKmAwal); kmAwalEl.addEventListener('input', validateKmAwal); }

            const unitEdit = document.getElementById('unit_mobil_edit');
            const platEdit = document.getElementById('plat_nomor_edit');
            if (unitEdit && platEdit) {
                unitEdit.addEventListener('change', function () {
                    platEdit.value = this.options[this.selectedIndex].getAttribute('data-plate') || '';
                });
            }

            const dispEdit = document.getElementById('km_awal_edit_display');
            const hidEdit = document.getElementById('km_awal_edit');
            if (dispEdit && hidEdit) {
                function fmtEdit() { const r = dispEdit.value.replace(/\D/g, ''); hidEdit.value = r; dispEdit.value = r ? parseInt(r).toLocaleString('id-ID') : ''; }
                dispEdit.addEventListener('input', function () {
                    const r = this.value.replace(/\D/g, ''); const c = this.selectionStart; const pl = this.value.length;
                    this.value = r ? parseInt(r).toLocaleString('id-ID') : ''; hidEdit.value = r;
                    this.setSelectionRange(c + this.value.length - pl, c + this.value.length - pl);
                });
                dispEdit.addEventListener('blur', fmtEdit);
                dispEdit.addEventListener('keypress', e => { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
                if (dispEdit.value) fmtEdit();
            }

            const mainForm = document.getElementById('mainFormWrapper');
            if (mainForm) {
                mainForm.addEventListener('submit', function (e) {
                    if (kmAkhirEl && kmAkhirEl.value !== '' && !validateKm()) { e.preventDefault(); kmAkhirEl.focus(); return; }
                    if (kmAwalEl && !validateKmAwal()) { e.preventDefault(); kmAwalEl.focus(); }
                });
            }
        });
    </script>
</x-app-layout>