<x-app-layout title="Buat Pengajuan — SIPETRANS">
@php
    $total    = auth()->user()->transportRequests()->count();
    $pending  = auth()->user()->transportRequests()->where('status', 'diajukan')->count();
    $approved = auth()->user()->transportRequests()->where('status', 'diproses')->count();
    $inuse    = auth()->user()->transportRequests()->where('status', 'digunakan')->count();
    $done     = auth()->user()->transportRequests()->where('status', 'selesai')->count();
    $rejected = auth()->user()->transportRequests()->where('status', 'tidak_disetujui')->count();
@endphp

<div class="mx-auto px-3 pt-2 pb-20" style="max-width:min(42rem, 100%);" data-user-page>

    <style>
        .sp-choose-card { transition: all 0.25s ease; border: 1px solid #e2e8f0; }
        .sp-choose-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); border-color: #007774; }
        .sp-choose-card:hover svg.arrow-icon { color: #007774 !important; transform: translateX(4px); transition: all 0.25s ease; }
        .sp-choose-card svg.arrow-icon { transition: all 0.25s ease; }
        
        @media (max-width: 576px) {
            .mx-auto { padding-top: 0.25rem !important; }
            .user-stats-container { padding: 0.75rem 0.5rem !important; margin-bottom: 0.5rem !important; }
            .stats-grid { gap: 0.4rem !important; }
            .stat-card { padding: 0.6rem 0.4rem !important; border-radius: 0.5rem !important; }
            .stat-icon { width: 1.9rem !important; height: 1.9rem !important; }
            .stat-icon svg { width: 17px !important; height: 17px !important; }
            .stat-value { font-size: 1.1rem !important; }
            .stat-label { font-size: 0.6rem !important; }
            .sp-choose-card { padding: 0.7rem 0.9rem !important; margin-bottom: 0.4rem !important; }
            .sp-choose-card svg { width: 22px !important; height: 22px !important; }
            .text-sm { font-size: 0.75rem !important; }
            .text-xs { font-size: 0.65rem !important; }
        }
    </style>

    {{-- ── GREETING WITH ILLUSTRATION ── --}}
    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
        <div class="flex-grow-1">
            <h1 class="fw-bold text-slate-900 mb-0" style="font-size:0.95rem; line-height:1.2;">Halo, {{ auth()->user()->full_name }} 👋</h1>
            <p class="text-xxs text-slate-500 mt-1 mb-0" style="font-size:0.7rem;">Pilih jenis transportasi yang Anda butuhkan.</p>
        </div>
        {{-- Small Hero Illustration --}}
        <div style="flex-shrink: 0; width: 65px; height: 52px; background: linear-gradient(135deg, #e0f7f6 0%, #f0fbf8 100%); border-radius: 0.6rem; position: relative; overflow: hidden;">
        <svg style="width: 100%; height: 100%; position: absolute; top: 0; left: 0;" viewBox="0 0 150 100" xmlns="http://www.w3.org/2000/svg">
            <!-- Grass -->
            <ellipse cx="75" cy="95" rx="65" ry="8" fill="#d1fae5" opacity="0.5"/>
            
            <!-- Trees -->
            <rect x="5" y="60" width="4" height="25" fill="#4b5563" opacity="0.3"/>
            <ellipse cx="7" cy="52" rx="8" ry="12" fill="#6ee7b7" opacity="0.4"/>
            
            <rect x="130" y="65" width="3" height="20" fill="#4b5563" opacity="0.3"/>
            <ellipse cx="131.5" cy="60" rx="6" ry="10" fill="#a7f3d0" opacity="0.4"/>
            
            <!-- Van -->
            <rect x="40" y="55" width="50" height="30" rx="3" fill="#007774"/>
            <rect x="52" y="50" width="15" height="12" rx="1" fill="#007774"/>
            <rect x="56" y="53" width="8" height="7" fill="#ecfdf5" opacity="0.7"/>
            
            <!-- Wheels -->
            <circle cx="52" cy="87" r="5" fill="#1e293b"/>
            <circle cx="52" cy="87" r="3" fill="#64748b"/>
            <circle cx="78" cy="87" r="5" fill="#1e293b"/>
            <circle cx="78" cy="87" r="3" fill="#64748b"/>
            
            <!-- Location Pin -->
            <circle cx="110" cy="35" r="7" fill="#007774"/>
            <path d="M110 35 L114 48 Q110 51 106 48 Z" fill="#007774"/>
            <circle cx="110" cy="35" r="2" fill="white"/>
        </svg>
        </div>
    </div>

    {{-- ── STATS GRID ── --}}
    <div class="user-stats-container mb-3">
        <div class="text-xxs fw-600 text-slate-600 mb-2 text-uppercase" style="letter-spacing:.05em; padding: 0 0.5rem; font-size: 0.6rem;">Ringkasan</div>
        
        <div class="stats-grid">
            {{-- Total --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd;">
                    <div class="stat-icon" style="background: rgba(37, 99, 235, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #2563eb;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #1d4ed8;">{{ $total }}</p>
                    <p class="stat-label">Total</p>
                </div>
            </div>

            {{-- Menunggu --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #fef9c3 0%, #fde68a 100%); border: 1px solid #fcd34d;">
                    <div class="stat-icon" style="background: rgba(251, 146, 60, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #f97316;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #ca8a04;">{{ $pending }}</p>
                    <p class="stat-label">Menunggu</p>
                </div>
            </div>

            {{-- Disetujui --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); border: 1px solid #d8b4fe;">
                    <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #a855f7;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #7e22ce;">{{ $approved }}</p>
                    <p class="stat-label">Disetujui</p>
                </div>
            </div>

            {{-- Digunakan --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); border: 1px solid #99f6e4;">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #10b981;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #0891b2;">{{ $inuse }}</p>
                    <p class="stat-label">Digunakan</p>
                </div>
            </div>

            {{-- Selesai --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 1px solid #86efac;">
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #22c55e;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #16a34a;">{{ $done }}</p>
                    <p class="stat-label">Selesai</p>
                </div>
            </div>

            {{-- Ditolak --}}
            <div class="stat-card-wrapper">
                <div class="stat-card" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 1px solid #fca5a5;">
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15);">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #ef4444;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <p class="stat-value" style="color: #dc2626;">{{ $rejected }}</p>
                    <p class="stat-label">Ditolak</p>
                </div>
            </div>
        </div>
    </div>{{-- /stats wrapper --}}

    {{-- ── PILIH JENIS ── --}}
    <div class="mb-3 sp-choose-cards-grid">
        {{-- Ambulance --}}
        <a href="{{ route('pengajuan.ambulance.create') }}"
           class="sp-choose-card d-flex align-items-center gap-3 bg-white rounded-lg px-4 py-4 shadow-sm text-decoration-none mb-3"
           style="border-left: 4px solid #ef4444; gap: 0.7rem !important;">
            <div class="d-flex align-items-center justify-content-center rounded-lg shrink-0"
                 style="width:2.8rem; height:2.8rem; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); flex-shrink: 0;">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #dc2626;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.362 5.313L19 8.5H4l-.362-3.187A1.5 1.5 0 014.5 3h15a1.5 1.5 0 011.362 2.313z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5v8a1.5 1.5 0 001.5 1.5h13a1.5 1.5 0 001.5-1.5v-8M7.5 12v5m3-5v5m3-5v5"/>
                </svg>
            </div>
            <div class="flex-grow-1 min-w-0">
                <p class="text-sm fw-bold text-slate-800 mb-0" style="font-size:0.9rem;">Ambulance</p>
                <p class="text-xs text-slate-500 mb-0 mt-1" style="font-size:0.75rem;">Layanan darurat &amp; rujukan medis</p>
            </div>
            <svg class="text-slate-300 shrink-0 arrow-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Mobil Umum --}}
        <a href="{{ route('pengajuan.umum.create') }}"
           class="sp-choose-card d-flex align-items-center gap-3 bg-white rounded-lg px-4 py-4 shadow-sm text-decoration-none"
           style="border-left: 4px solid #16a34a; gap: 0.7rem !important;">
            <div class="d-flex align-items-center justify-content-center rounded-lg shrink-0"
                 style="width:2.8rem; height:2.8rem; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); flex-shrink: 0;">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: #16a34a;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3z"/>
                </svg>
            </div>
            <div class="flex-grow-1 min-w-0">
                <p class="text-sm fw-bold text-slate-800 mb-0" style="font-size:0.9rem;">Mobil Umum</p>
                <p class="text-xs text-slate-500 mb-0 mt-1" style="font-size:0.75rem;">Transportasi operasional &amp; dinas</p>
            </div>
            <svg class="text-slate-300 shrink-0 arrow-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

</div>
</x-app-layout>
