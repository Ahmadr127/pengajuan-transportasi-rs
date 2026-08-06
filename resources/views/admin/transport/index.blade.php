<x-app-layout title="Daftar Pengajuan — SIPETRANS">
    <div class="container-fluid px-3 px-sm-4 pt-4 pt-sm-5 pb-4">
        <h1 class="fs-5 fw-bold text-slate-800">
            Daftar Pengajuan Transportasi
        </h1>
        <p class="text-slate-500 mt-1 text-xs">
            Kelola semua pengajuan mobil umum dan ambulance
        </p>

        @if (session('success'))
            <div class="alert alert-sp-success mt-3 py-2 px-3 rounded fw-500 text-xs">
                {{ session('success') }}
            </div>
        @endif

        <div class="mt-3 mt-sm-4">
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.transport.index') }}" class="sp-card p-2 mb-3">
                <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
                    <select name="jenis" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="umum" @selected(request('jenis') === 'umum')>Mobil Umum</option>
                        <option value="ambulance" @selected(request('jenis') === 'ambulance')>Ambulance</option>
                    </select>

                    <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diajukan" @selected(!request()->has('status') || request('status') === 'diajukan')>Menunggu (Diajukan)</option>
                        <option value="diproses" @selected(request('status') === 'diproses')>Disetujui</option>
                        <option value="digunakan" @selected(request('status') === 'digunakan')>Digunakan</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                        <option value="tidak_disetujui" @selected(request('status') === 'tidak_disetujui')>Tidak Disetujui</option>
                    </select>

                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                           class="form-control form-control-sm" style="width:auto;"
                           onchange="this.form.submit()">

                    @if(request()->hasAny(['jenis', 'status', 'tanggal']))
                        <a href="{{ route('admin.transport.index') }}" class="btn btn-sm btn-outline-secondary px-3 py-1 text-xs fw-500">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table List (Desktop) / Card List (Mobile) -->
            <div class="sp-card overflow-hidden">
                <!-- Desktop Table View -->
                <div class="d-none d-md-block overflow-x-auto">
                    <table class="sp-table w-100">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Pemohon</th>
                                <th>Jenis</th>
                                <th>Tanggal &amp; Waktu</th>
                                <th>Dibuat</th>
                                <th>Kendaraan</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.transport.show', $item) }}"
                                           class="font-mono fw-700 text-teal-700 bg-teal-50 border border-teal-200 px-2 py-1 rounded d-inline-block text-decoration-none"
                                           style="font-size:.75rem;letter-spacing:.03em;">
                                            {{ $item->nomor_pengajuan }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-600 text-xs text-slate-900">{{ $item->user->full_name ?? $item->pemohon_nama }}</div>
                                        <div class="text-xxs text-slate-500">{{ $item->user->unit_kerja ?? $item->pemohon_unit }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge status-badge {{ $item->jenis === 'ambulance' ? 'bg-red-100 text-red-700 border border-red-200' : 'badge-emerald' }}">
                                                {{ ucfirst($item->jenis) }}
                                            </span>
                                            @if($item->prioritas === 'segera')
                                                <span class="badge status-badge badge-red">CITO</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-xs text-slate-700 fw-500 whitespace-nowrap">{{ $item->tanggal->format('d/m/Y') }}</div>
                                        <div class="text-xxs text-slate-500 whitespace-nowrap">{{ substr($item->jam, 0, 5) }}
                                            @if($item->jam_sampai) - {{ substr($item->jam_sampai, 0, 5) }} @else - selesai @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-xs text-slate-700 fw-500 whitespace-nowrap">{{ $item->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xxs text-slate-500 whitespace-nowrap">{{ $item->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        @if($item->unit_mobil)
                                            <div class="text-xs text-slate-700 fw-500 text-capitalize">{{ str_replace('_', ' ', $item->unit_mobil) }}</div>
                                            @if($item->plat_nomor)
                                                <div class="text-xxs text-slate-500 font-mono">{{ $item->plat_nomor }}</div>
                                            @endif
                                        @else
                                            <span class="text-xxs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->alamat_tujuan)
                                            <div class="text-xs text-slate-600" style="max-width:16rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $item->alamat_tujuan }}</div>
                                        @else
                                            <span class="text-xxs text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeMap = ['diajukan' => 'badge-amber', 'diproses' => 'badge-blue', 'digunakan' => 'badge-cyan', 'selesai' => 'badge-emerald', 'tidak_disetujui' => 'badge-red'];
                                            $badgeClass = $badgeMap[$item->status] ?? 'badge-slate';
                                            $label = match($item->status) {
                                                'diproses' => 'Disetujui',
                                                'digunakan' => 'Digunakan',
                                                default => ucfirst($item->status)
                                            };
                                        @endphp
                                        <span class="badge status-badge {{ $badgeClass }} whitespace-nowrap">
                                            {{ $label }}
                                        </span>
                                        @if($item->status === 'diajukan' && !$item->signature_pemohon)
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        @elseif($item->status === 'diproses' && !$item->signature_pengelola_1)
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        @elseif($item->status === 'digunakan' && !$item->signature_driver)
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.transport.show', $item) }}" class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1 whitespace-nowrap" style="font-size:.625rem;">
                                            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="white" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-5 text-center">
                                        <svg style="width:2.5rem;height:2.5rem;" class="text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-slate-500 text-xs fw-500 mb-0">Belum ada data pengajuan transportasi.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none divide-y divide-slate-200">
                    @forelse($items as $item)
                        <div class="p-3" style="transition:background .1s;">
                            <!-- Header -->
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-600 text-sm text-slate-900 truncate">{{ $item->user->full_name ?? $item->pemohon_nama }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ $item->user->unit_kerja ?? $item->pemohon_unit }}</div>
                                </div>
                                @php
                                    $badgeMap2 = ['diajukan' => 'badge-amber', 'diproses' => 'badge-blue', 'digunakan' => 'badge-cyan', 'selesai' => 'badge-emerald', 'tidak_disetujui' => 'badge-red'];
                                    $badgeClass2 = $badgeMap2[$item->status] ?? 'badge-slate';
                                    $label2 = match($item->status) {
                                        'diproses' => 'Disetujui',
                                        'digunakan' => 'Digunakan',
                                        'tidak_disetujui' => 'Tidak Disetujui',
                                        default => ucfirst($item->status)
                                    };
                                @endphp
                                <span class="badge status-badge {{ $badgeClass2 }} whitespace-nowrap">
                                    {{ $label2 }}
                                </span>
                            </div>

                            <!-- Badges -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge status-badge {{ $item->jenis === 'ambulance' ? 'bg-red-100 text-red-700' : 'badge-emerald' }}">
                                    {{ ucfirst($item->jenis) }}
                                </span>
                                @if($item->prioritas === 'segera')
                                    <span class="badge status-badge badge-red">CITO</span>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="mb-3 text-xs">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-slate-700 fw-500">{{ $item->tanggal->format('d M Y') }}</span>
                                    <span class="text-slate-500">{{ $item->jam }} - {{ $item->jam_sampai }}</span>
                                </div>
                                <div class="text-xxs text-slate-400 mb-2">Dibuat: {{ $item->created_at->format('d M Y, H:i') }}</div>

                                @if($item->unit_mobil)
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span class="text-slate-700 text-capitalize">{{ str_replace('_', ' ', $item->unit_mobil) }}</span>
                                        @if($item->plat_nomor)
                                            <span class="text-slate-500 font-mono text-xxs">({{ $item->plat_nomor }})</span>
                                        @endif
                                    </div>
                                @endif

                                @if($item->alamat_tujuan)
                                    <div class="d-flex align-items-start gap-2">
                                        <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-slate-600 flex-grow-1" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $item->alamat_tujuan }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Button -->
                            <div class="d-flex flex-column gap-2">
                                @if(($item->status === 'diajukan' && !$item->signature_pemohon) || ($item->status === 'diproses' && !$item->signature_pengelola_1) || ($item->status === 'digunakan' && !$item->signature_driver))
                                    <div class="text-xxs text-amber-600 fw-600 text-center d-flex align-items-center justify-content-center gap-1">
                                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        Tanda tangan belum lengkap
                                    </div>
                                @endif
                                <a href="{{ route('admin.transport.show', $item) }}" class="btn btn-sp-primary btn-sm w-100 text-center text-xs fw-600">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center">
                            <svg style="width:3rem;height:3rem;" class="text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500 text-sm fw-500 mb-0">Belum ada data pengajuan transportasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
