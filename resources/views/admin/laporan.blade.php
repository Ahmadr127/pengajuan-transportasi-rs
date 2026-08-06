<x-app-layout title="Laporan — SIPETRANS">
    <div class="container-fluid px-3 px-sm-4 pt-4 pb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fs-5 fw-bold text-slate-900 mb-0">Laporan Pengajuan</h1>
                <p class="text-xs text-slate-500 mt-1 mb-0">Data lengkap seluruh pengajuan transportasi</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.laporan.print', request()->query()) }}"
                   target="_blank"
                   class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print PDF
                </a>
                <a href="{{ route('admin.laporan.export', request()->query()) }}"
                   class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- Tabel dengan filter inline di thead --}}
        <form method="GET" action="{{ route('admin.laporan') }}" id="laporan-filter-form">
        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100" style="min-width:1800px;">
                    <thead>
                        {{-- Baris 1: Filter inputs --}}
                        <tr class="bg-slate-50 border-bottom border-slate-200">
                            <th class="px-2 py-2">
                                <input type="text" name="nomor" value="{{ request('nomor') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <div class="d-flex flex-column gap-1">
                                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                                           class="form-control form-control-sm" style="font-size:.625rem;">
                                    <input type="date" name="tanggal_sampai_filter" value="{{ request('tanggal_sampai_filter') }}"
                                           class="form-control form-control-sm" style="font-size:.625rem;">
                                </div>
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="pemohon" value="{{ request('pemohon') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="unit_kerja" value="{{ request('unit_kerja') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <select name="jenis" class="form-select form-select-sm" style="font-size:.625rem;">
                                    <option value="">Semua</option>
                                    <option value="umum" @selected(request('jenis') === 'umum')>Umum</option>
                                    <option value="ambulance" @selected(request('jenis') === 'ambulance')>Ambulance</option>
                                </select>
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="keperluan" value="{{ request('keperluan') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <input type="text" name="tujuan" value="{{ request('tujuan') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="unit_mobil" value="{{ request('unit_mobil') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="plat_nomor" value="{{ request('plat_nomor') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <input type="text" name="supir" value="{{ request('supir') }}" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <div class="d-flex flex-column gap-1">
                                    <select name="status" class="form-select form-select-sm" style="font-size:.625rem;">
                                        <option value="">Semua</option>
                                        <option value="diajukan" @selected(request('status') === 'diajukan')>Diajukan</option>
                                        <option value="diproses" @selected(request('status') === 'diproses')>Disetujui</option>
                                        <option value="digunakan" @selected(request('status') === 'digunakan')>Digunakan</option>
                                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                                        <option value="tidak_disetujui" @selected(request('status') === 'tidak_disetujui')>Tdk Disetujui</option>
                                    </select>
                                    <select name="prioritas" class="form-select form-select-sm" style="font-size:.625rem;">
                                        <option value="">Semua Prioritas</option>
                                        <option value="biasa" @selected(request('prioritas') === 'biasa')>Biasa</option>
                                        <option value="segera" @selected(request('prioritas') === 'segera')>CITO</option>
                                    </select>
                                </div>
                            </th>
                        </tr>
                        {{-- Baris 2: Label kolom --}}
                        <tr>
                            <th class="whitespace-nowrap">No. Pengajuan</th>
                            <th class="whitespace-nowrap">Dibuat</th>
                            <th class="whitespace-nowrap">Nama Pemohon</th>
                            <th class="whitespace-nowrap">Unit Kerja</th>
                            <th class="whitespace-nowrap">Jenis</th>
                            <th class="whitespace-nowrap">Keperluan</th>
                            <th class="whitespace-nowrap">Tgl Berangkat</th>
                            <th class="whitespace-nowrap">Jam</th>
                            <th class="whitespace-nowrap">Tujuan</th>
                            <th class="whitespace-nowrap">Unit Kendaraan</th>
                            <th class="whitespace-nowrap">Plat Nomor</th>
                            <th class="whitespace-nowrap">NIP Driver</th>
                            <th class="whitespace-nowrap">Nama Driver</th>
                            <th class="whitespace-nowrap">KM Awal</th>
                            <th class="whitespace-nowrap">KM Akhir</th>
                            <th class="whitespace-nowrap">Jarak</th>
                            <th class="whitespace-nowrap">Tgl Kembali</th>
                            <th class="whitespace-nowrap">Jam Tiba</th>
                            <th class="whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            @php
                                $badgeMap = [
                                    'diajukan'       => 'badge-amber',
                                    'diproses'       => 'badge-blue',
                                    'digunakan'      => 'badge-cyan',
                                    'selesai'        => 'badge-emerald',
                                    'tidak_disetujui'=> 'badge-red',
                                ];
                                $statusLabel = match($item->status) {
                                    'diproses'       => 'Disetujui',
                                    'tidak_disetujui'=> 'Tdk Disetujui',
                                    default          => ucfirst($item->status),
                                };
                                $nipSupir = $item->driver && $item->driver->user ? $item->driver->user->nip : null;
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap">
                                    <span class="font-mono fw-600 text-slate-700">{{ $item->nomor_pengajuan }}</span>
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <div>{{ $item->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xxs text-slate-400">{{ $item->created_at->format('H:i') }}</div>
                                </td>
                                <td class="whitespace-nowrap">
                                    <div class="fw-500 text-slate-900">{{ $item->user->full_name ?? $item->pemohon_nama }}</div>
                                    @if($item->user && $item->user->isPriority())
                                        <span class="badge" style="font-size:.5625rem;background:#ede9fe;color:#6d28d9;">PRIORITAS</span>
                                    @endif
                                </td>
                                <td class="text-slate-600 whitespace-nowrap">
                                    {{ $item->user->unit_kerja ?? $item->pemohon_unit ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="badge status-badge {{ $item->jenis === 'ambulance' ? 'bg-red-100 text-red-700' : 'badge-emerald' }}">
                                        {{ ucfirst($item->jenis) }}
                                    </span>
                                    @if($item->prioritas === 'segera')
                                        <span class="badge status-badge badge-red ms-1">CITO</span>
                                    @endif
                                </td>
                                <td class="text-slate-600 truncate" style="max-width:7.5rem;" title="{{ $item->keperluan }}">
                                    {{ $item->keperluan ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap text-slate-700">
                                    {{ $item->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    {{ substr($item->jam, 0, 5) }}
                                    @if($item->jam_sampai)
                                        <span class="text-slate-400">-</span> {{ substr($item->jam_sampai, 0, 5) }}
                                    @endif
                                </td>
                                <td class="text-slate-600 truncate" style="max-width:8.75rem;" title="{{ $item->alamat_tujuan }}">
                                    {{ $item->alamat_tujuan ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap text-slate-700">
                                    @if($item->unit_mobil)
                                        <div class="fw-500 text-capitalize">{{ str_replace('_', ' ', $item->unit_mobil) }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap font-mono text-slate-600">
                                    {{ $item->plat_nomor ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap font-mono text-slate-600">
                                    {{ $nipSupir ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    {{ $item->driver->name ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap text-slate-600 text-end">
                                    {{ $item->km_awal ? number_format($item->km_awal, 0, ',', '.') : '-' }}
                                </td>
                                <td class="whitespace-nowrap text-slate-600 text-end">
                                    {{ $item->km_akhir ? number_format($item->km_akhir, 0, ',', '.') : '-' }}
                                </td>
                                <td class="whitespace-nowrap text-end">
                                    @if($item->km_awal && $item->km_akhir)
                                        <span class="fw-600 text-emerald-700">+{{ number_format($item->km_akhir - $item->km_awal, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    @if($item->tanggal_sampai)
                                        {{ $item->tanggal_sampai->format('d/m/Y') }}
                                    @elseif($item->status === 'selesai')
                                        {{ $item->updated_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-slate-400 text-xxs fst-italic">Sampai Selesai</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    {{ $item->jam_kedatangan ?? '-' }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="badge status-badge {{ $badgeMap[$item->status] ?? 'badge-slate' }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="19" class="px-3 py-5 text-center">
                                    <svg style="width:2.5rem;height:2.5rem;" class="text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-slate-500 text-xs fw-500 mb-0">Tidak ada data yang sesuai filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="px-4 py-3 bg-slate-50 border-top border-slate-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
        </form>

        <div class="mt-2 text-xxs text-slate-400">
            Menampilkan {{ $items->firstItem() ?? 0 }}&ndash;{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('laporan-filter-form');
            if (!form) return;

            let debounceTimer;

            function submitClean(delay) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.querySelectorAll('input, select').forEach(el => {
                        if (el.value === '') el.disabled = true;
                    });
                    form.submit();
                }, delay ?? 500);
            }

            form.querySelectorAll('input[type="text"], input[type="date"]').forEach(function (input) {
                input.addEventListener('input', () => submitClean(500));
            });

            form.querySelectorAll('select').forEach(function (select) {
                select.addEventListener('change', () => submitClean(0));
            });
        });
    </script>
</x-app-layout>
