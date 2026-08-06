<x-app-layout title="Profil Driver — SIPETRANS">
<div class="sp-content pb-5">
    <div class="container py-3" style="max-width:480px;">

        {{-- Avatar + Name --}}
        <div class="sp-card mb-3 px-4 py-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                     style="width:3.5rem;height:3.5rem;background:linear-gradient(135deg,#007774,#81BD41);">
                    <svg style="width:1.75rem;height:1.75rem;" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="text-sm fw-700 text-slate-800 truncate">{{ $driver->name }}</div>
                    <div class="text-xs text-slate-500">Driver</div>
                    <span class="badge mt-1" style="background:rgba(0,119,116,0.1);color:#007774;font-size:.65rem;">
                        {{ $driver->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>
            </div>

            {{-- Stats --}}
            <div class="d-flex gap-2 mt-3">
                <div class="flex-fill text-center rounded py-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="text-base fw-700" style="color:#007774;">{{ $totalTugas }}</div>
                    <div class="text-xxs text-slate-500">Total</div>
                </div>
                <div class="flex-fill text-center rounded py-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="text-base fw-700 text-warning">{{ $tugasAktif }}</div>
                    <div class="text-xxs text-slate-500">Aktif</div>
                </div>
                <div class="flex-fill text-center rounded py-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="text-base fw-700 text-success">{{ $tugasSelesai }}</div>
                    <div class="text-xxs text-slate-500">Selesai</div>
                </div>
            </div>
        </div>

        {{-- Info Detail --}}
        <div class="sp-card mb-3">
            <div class="px-3 py-2 border-bottom border-slate-100">
                <span class="text-xs fw-600 text-slate-600">Informasi Driver</span>
            </div>
            <div class="px-3">
                @php
                    $rows = [
                        ['label' => 'Nama',        'value' => $driver->name],
                        ['label' => 'No. Telepon', 'value' => $driver->phone ?? '-'],
                        ['label' => 'No. SIM',     'value' => $driver->license_number ?? '-'],
                        ['label' => 'Status',      'value' => $driver->is_active ? 'Aktif' : 'Tidak Aktif'],
                    ];
                    if ($driver->user) {
                        $rows[] = ['label' => 'Username', 'value' => $driver->user->username ?? '-'];
                        $rows[] = ['label' => 'NIP',      'value' => $driver->user->nip ?? '-'];
                    }
                @endphp
                @foreach($rows as $row)
                <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom border-slate-100' : '' }}">
                    <span class="text-xs text-slate-400 shrink-0" style="width:7rem;">{{ $row['label'] }}</span>
                    <span class="text-xs fw-500 text-slate-700">{{ $row['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn w-100 d-flex align-items-center justify-content-center gap-2 text-sm fw-600"
                    style="background:#ef4444;color:#fff;border:none;border-radius:.75rem;padding:.625rem;">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar
            </button>
        </form>

    </div>
</div>
</x-app-layout>
