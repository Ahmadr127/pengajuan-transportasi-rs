<x-app-layout title="Riwayat Tugas — SIPETRANS">
    <div class="mx-auto px-3 pt-3 pb-20" style="max-width:64rem;" data-driver-page>

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-900 mb-0" style="font-size:1rem;">Riwayat Perjalanan</h1>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <p class="text-xxs text-slate-500 mb-0">{{ $driver->name }}</p>
                    <span class="badge-slate">{{ $historyRequests->total() }} total</span>
                </div>
            </div>
            <a href="{{ route('driver.dashboard') }}"
               class="d-inline-flex align-items-center gap-1 px-3 py-2 rounded border border-slate-200 bg-white text-xs fw-500 text-slate-600 text-decoration-none">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>

        <div class="sp-card overflow-hidden">

            {{-- Filter — selalu tampil --}}
            <form method="GET" action="{{ route('driver.history') }}" id="driver-history-filter"
                  class="d-flex flex-wrap gap-2 px-3 py-2 border-bottom border-slate-200 bg-slate-50">
                <select name="status" class="form-select form-select-sm text-xs" style="width:auto;">
                    <option value="">Semua Status</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    <option value="tidak_disetujui" @selected(request('status') === 'tidak_disetujui')>Ditolak</option>
                </select>
                <select name="jenis" class="form-select form-select-sm text-xs" style="width:auto;">
                    <option value="">Semua Jenis</option>
                    <option value="umum" @selected(request('jenis') === 'umum')>Umum</option>
                    <option value="ambulance" @selected(request('jenis') === 'ambulance')>Ambulance</option>
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                       class="form-control form-control-sm text-xs" style="width:auto;">
                @if(request()->hasAny(['status','tanggal','jenis']))
                    <a href="{{ route('driver.history') }}"
                       class="d-inline-flex align-items-center px-2 py-1 rounded border border-slate-200 bg-white text-xxs fw-500 text-slate-500 text-decoration-none">
                        Reset
                    </a>
                @endif
            </form>

            @if($historyRequests->isEmpty())
                <div class="p-5 text-center">
                    <svg class="text-slate-300 mx-auto mb-2" width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-xs text-slate-500 fw-500 mb-0">
                        {{ request()->hasAny(['status','tanggal','jenis']) ? 'Tidak ada data sesuai filter' : 'Belum ada riwayat perjalanan' }}
                    </p>
                </div>
            @else

                {{-- TABLE: desktop --}}
                <div class="d-none d-md-block overflow-x-auto">
                    <table class="sp-table w-100">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Pemohon</th>
                                <th>Jenis</th>
                                <th>Tanggal &amp; Waktu</th>
                                <th>Kendaraan</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyRequests as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('driver.detail', $item) }}"
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
                                                'tidak_disetujui' => 'Ditolak',
                                                default => ucfirst($item->status)
                                            };
                                        @endphp
                                        <span class="badge status-badge {{ $badgeClass }} whitespace-nowrap">
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('driver.detail', $item) }}" class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1 whitespace-nowrap" style="font-size:.625rem;">
                                            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="white" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- CARDS: mobile --}}
                <div class="d-md-none divide-y divide-slate-200">
                    @foreach($historyRequests as $item)
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
                                        'tidak_disetujui' => 'Ditolak',
                                        default => ucfirst($item->status)
                                    };
                                @endphp
                                <span class="badge status-badge {{ $badgeClass2 }} whitespace-nowrap">
                                    {{ $label2 }}
                                </span>
                            </div>

                            <!-- Badges -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="font-mono text-xs fw-600 text-teal-700 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded">
                                    {{ $item->nomor_pengajuan }}
                                </span>
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
                                    <span class="text-slate-500">{{ substr($item->jam, 0, 5) }} - {{ $item->jam_sampai ? substr($item->jam_sampai, 0, 5) : 'selesai' }}</span>
                                </div>

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
                                <a href="{{ route('driver.detail', $item) }}" class="btn btn-sp-primary btn-sm w-100 text-center text-xs fw-600">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($historyRequests->hasPages())
                    <div class="px-3 py-3 bg-slate-50 border-top border-slate-200">
                        {{ $historyRequests->links() }}
                    </div>
                @endif

            @endif
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('driver-history-filter');
            if (!form) return;
            let timer;
            function submitClean(delay) {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    form.querySelectorAll('input, select').forEach(el => { if (el.value === '') el.disabled = true; });
                    form.submit();
                }, delay);
            }
            form.querySelectorAll('input[type="date"]').forEach(el => el.addEventListener('input', () => submitClean(300)));
            form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => submitClean(0)));
        });
    </script>

</x-app-layout>
