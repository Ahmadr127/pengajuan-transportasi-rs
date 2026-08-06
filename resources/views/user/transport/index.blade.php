<x-app-layout title="Riwayat Pengajuan — SIPETRANS">
<div class="mx-auto px-2 px-sm-3 pt-2 pb-4" style="max-width:56rem;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
        <div>
            <h1 class="fw-bold text-slate-900 mb-0" style="font-size:1rem;">Riwayat Pengajuan</h1>
            <p class="text-xxs text-slate-500 mt-1 mb-0">Daftar pengajuan transportasi yang telah Anda buat</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard') }}"
               class="d-inline-flex align-items-center gap-1 rounded px-2 py-1 text-xxs fw-600 text-white text-decoration-none"
               style="background:#059669;">
                <svg width="12" height="12" fill="white" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                <span class="d-none d-sm-inline">Pengajuan Baru</span>
                <span class="d-sm-none">Baru</span>
            </a>
            <a href="{{ route('dashboard') }}"
               class="d-inline-flex align-items-center gap-1 rounded border border-slate-300 bg-white px-2 py-1 text-xxs fw-600 text-slate-600 text-decoration-none">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form action="{{ route('pengajuan.index') }}" method="GET" id="user-filter-form"
          class="sp-card px-3 py-2 mb-2 d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label text-xxs fw-600 text-slate-600 mb-1 d-block">Jenis</label>
            <select name="jenis" class="form-select form-select-sm" style="width:auto;">
                <option value="">Semua</option>
                <option value="ambulance" {{ request('jenis')=='ambulance'?'selected':'' }}>Ambulance</option>
                <option value="umum" {{ request('jenis')=='umum'?'selected':'' }}>Umum</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xxs fw-600 text-slate-600 mb-1 d-block">Status</label>
            <select name="status" class="form-select form-select-sm" style="width:auto;">
                <option value="">Semua</option>
                <option value="diajukan" {{ request('status')=='diajukan'?'selected':'' }}>Diajukan</option>
                <option value="diproses" {{ request('status')=='diproses'?'selected':'' }}>Disetujui</option>
                <option value="digunakan" {{ request('status')=='digunakan'?'selected':'' }}>Digunakan</option>
                <option value="selesai" {{ request('status')=='selesai'?'selected':'' }}>Selesai</option>
                <option value="tidak_disetujui" {{ request('status')=='tidak_disetujui'?'selected':'' }}>Tidak Disetujui</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xxs fw-600 text-slate-600 mb-1 d-block">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                   class="form-control form-control-sm" style="width:auto;">
        </div>
        @if(request()->hasAny(['jenis','status','tanggal']))
        <a href="{{ route('pengajuan.index') }}"
           class="d-inline-flex align-items-center px-2 py-1 rounded border border-slate-200 bg-white text-xxs fw-500 text-slate-500 text-decoration-none" style="margin-top:1.25rem;">
            Reset
        </a>
        @endif
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('user-filter-form');
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

    {{-- Table / Cards --}}
    <div class="sp-card">

        {{-- Desktop Table --}}
        <div class="d-none d-md-block overflow-x-auto">
            <table class="sp-table w-100">
                <thead>
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Jenis & Keperluan</th>
                        <th>Jadwal</th>
                        <th>Dibuat</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                    <tr>
                        <td>
                            <a href="{{ route('pengajuan.success', $item) }}"
                               class="font-mono fw-700 text-teal-700 bg-teal-50 border border-teal-200 px-2 py-1 rounded d-inline-block text-decoration-none"
                               style="font-size:.75rem;letter-spacing:.03em;">
                                {{ $item->nomor_pengajuan }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 flex-wrap mb-1">
                                <span class="badge status-badge {{ $item->jenis === 'ambulance' ? 'bg-red-100 text-red-700 border border-red-200' : 'badge-emerald' }}">
                                    {{ ucfirst($item->jenis) }}
                                </span>
                                @if($item->prioritas === 'segera')
                                    <span class="badge status-badge badge-red">CITO</span>
                                @endif
                            </div>
                            @if($item->keperluan)
                                <div class="text-xs text-slate-600 fw-500 truncate" title="{{ ucfirst($item->keperluan) }}" style="max-width:16rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                    {{ ucfirst($item->keperluan) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="text-xs text-slate-700 fw-500 whitespace-nowrap">
                                {{ $item->tanggal?->format('d/m/Y') }} {{ substr($item->jam, 0, 5) }}
                            </div>
                            <div class="text-xxs text-slate-500 whitespace-nowrap">
                                @if($item->tanggal_sampai && $item->jam_sampai)
                                    s/d {{ $item->tanggal_sampai?->format('d/m/Y') }} {{ substr($item->jam_sampai, 0, 5) }}
                                @else
                                    s/d selesai
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-xs text-slate-700 fw-500 whitespace-nowrap">{{ $item->created_at->format('d/m/Y') }}</div>
                            <div class="text-xxs text-slate-500 whitespace-nowrap">{{ $item->created_at->format('H:i') }}</div>
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
                            <a href="{{ route('pengajuan.success', $item) }}" class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1 whitespace-nowrap" style="font-size:.625rem;">
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
                        <td colspan="6" class="px-3 py-5 text-center">
                            <svg style="width:2.5rem;height:2.5rem;" class="text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500 text-xs fw-500 mb-0">Belum ada pengajuan transportasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="d-md-none divide-y divide-slate-200">
            @forelse ($items as $item)
                <div class="p-3" style="transition:background .1s;">
                    <!-- Header -->
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-600 text-sm text-slate-900 truncate">{{ $item->nomor_pengajuan }}</div>
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
                            <span class="text-slate-500">{{ $item->jam }} - {{ $item->jam_sampai ?? 'selesai' }}</span>
                        </div>
                        <div class="text-xxs text-slate-400 mb-2">Dibuat: {{ $item->created_at->format('d M Y, H:i') }}</div>

                        @if($item->keperluan)
                            <div class="d-flex align-items-start gap-2">
                                <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-slate-600 flex-grow-1" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ ucfirst($item->keperluan) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('pengajuan.success', $item) }}" class="btn btn-sp-primary btn-sm w-100 text-center text-xs fw-600">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center">
                    <svg style="width:3rem;height:3rem;" class="text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-slate-500 text-sm fw-500 mb-0">Belum ada pengajuan transportasi.</p>
                </div>
            @endforelse
        </div>

        @if($items->hasPages())
        <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
            {{ $items->links() }}
        </div>
        @endif
    </div>

</div>

{{-- Modal Pengajuan Berulang --}}
@if(session('recurring_success'))
<div id="recurringSuccessModal"
     class="position-fixed inset-0 d-flex align-items-center justify-content-center p-3"
     style="z-index:9999; background:rgba(0,0,0,0.45); backdrop-filter:blur(2px);">
    <div id="recurringModalBox"
         class="position-relative bg-white rounded shadow w-100 overflow-hidden"
         style="max-width:22rem; animation: modalPop 0.35s cubic-bezier(0.34,1.56,0.64,1) both;">
        <div style="height:.25rem; background:linear-gradient(to right, #007774, #81BD41);"></div>
        <div class="px-4 py-3 text-center">
            <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle"
                 style="width:3rem; height:3rem; background:#e6f7f6; border:2px solid #007774;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#007774" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h2 class="fw-bold text-slate-900 mb-1" style="font-size:.875rem;">Pengajuan Berulang Dibuat!</h2>
            <p class="text-xxs text-slate-500 mb-2">{{ session('recurring_success') }}</p>
            <p class="text-xxs text-indigo-600 fw-600 bg-indigo-50 border border-indigo-200 rounded px-2 py-1">
                🔁 Pengajuan akan dibuat otomatis setiap hari yang dipilih
            </p>
        </div>
        <div class="px-4 pb-3">
            <button onclick="closeRecurringModal()"
                    class="btn w-100 text-xs fw-600 text-white"
                    style="background:linear-gradient(to right, #007774, #009e9a); padding:.375rem;">
                Mengerti, Oke!
            </button>
        </div>
    </div>
</div>
<style>
    @keyframes modalPop {
        0%   { opacity:0; transform:scale(0.7) translateY(20px); }
        100% { opacity:1; transform:scale(1) translateY(0); }
    }
</style>
<script>
    function closeRecurringModal() {
        const modal = document.getElementById('recurringSuccessModal');
        const box = document.getElementById('recurringModalBox');
        if (box) { box.style.transition='opacity .2s,transform .2s'; box.style.opacity='0'; box.style.transform='scale(0.85)'; }
        setTimeout(() => { if (modal) modal.remove(); }, 220);
    }
    document.getElementById('recurringSuccessModal')?.addEventListener('click', function(e) { if (e.target===this) closeRecurringModal(); });
    setTimeout(closeRecurringModal, 8000);
</script>
@endif

</x-app-layout>
