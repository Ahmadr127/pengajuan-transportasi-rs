<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan - RS Azra</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f0f7f7; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3" style="min-height:100vh;">
    <div class="w-100" style="max-width:24rem;">

        <!-- Header -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="RS Azra" style="height:2.25rem; width:auto;">
            <div>
                <div class="text-xxs text-slate-500 leading-none">rs azra</div>
                <div class="text-xs fw-bold leading-tight" style="color:#007774;">Verifikasi Tanda Tangan</div>
            </div>
        </div>

        @if(!$found)
            <div class="bg-white rounded overflow-hidden" style="border:1px solid #fca5a5;">
                <div class="px-4 py-3 d-flex align-items-center gap-2" style="background-color:#ef4444;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                         style="width:2rem; height:2rem; background-color:rgba(255,255,255,.2);">
                        <svg class="text-white" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm fw-bold text-white mb-0">Tidak Valid</p>
                        <p class="text-xxs mb-0" style="color:#fecaca;">Tanda tangan tidak ditemukan</p>
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-xs text-slate-600 mb-0">QR Code ini tidak terdaftar. Dokumen mungkin tidak sah atau QR Code telah rusak.</p>
                </div>
            </div>

        @else
            <div class="bg-white rounded overflow-hidden" style="border:1px solid #b2d8d8;">

                <!-- Status Bar -->
                <div class="px-3 py-2 d-flex align-items-center gap-2" style="background-color:#007774;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                         style="width:2rem; height:2rem; background-color:rgba(255,255,255,.2);">
                        <svg class="text-white" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm fw-bold text-white mb-0">Tanda Tangan Valid</p>
                        <p class="text-xxs mb-0" style="color:#b2e0df;">Dokumen telah ditandatangani secara sah</p>
                    </div>
                </div>

                <div class="p-3">

                    <!-- Penandatangan -->
                    <div class="rounded p-2 mb-3" style="background-color:#e6f4f4; border:1px solid #b2d8d8;">
                        <p class="text-xxs fw-bold text-uppercase tracking-wider mb-2" style="color:#007774;">Penandatangan</p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                                 style="width:2rem; height:2rem; background-color:#007774;">
                                <svg class="text-white" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs fw-bold text-slate-900 truncate mb-0">{{ $signatureInfo['name'] }}</p>
                                <p class="text-xxs text-slate-500 truncate mb-0">{{ $signatureInfo['unit'] }}</p>
                                <p class="text-xxs fw-600 mb-0" style="color:#5C8727;">{{ $signatureInfo['role'] }}</p>
                            </div>
                        </div>
                        @if($signatureInfo['signed_at'])
                            <div class="d-flex align-items-center gap-1 mt-2 pt-2" style="border-top:1px solid #b2d8d8;">
                                <svg class="shrink-0" width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#007774;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xxs mb-0" style="color:#007774;">{{ $signatureInfo['signed_at']->format('d M Y, H:i') }} WIB</p>
                            </div>
                        @endif
                    </div>

                    <!-- Info Dokumen -->
                    <div class="bg-slate-50 rounded p-2" style="border:1px solid #e2e8f0;">
                        <p class="text-xxs fw-bold text-slate-500 text-uppercase tracking-wider mb-2">Detail Dokumen</p>
                        <div class="row g-2">
                            <div class="col-6">
                                <p class="text-xxs text-slate-400 mb-0">No. Pengajuan</p>
                                <p class="text-xs font-mono fw-bold text-slate-800 mb-0">{{ $transportRequest->nomor_pengajuan }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-xxs text-slate-400 mb-0">Jenis</p>
                                <p class="text-xs fw-600 text-slate-800 mb-0">{{ ucfirst($transportRequest->jenis) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-xxs text-slate-400 mb-0">Pemohon</p>
                                <p class="text-xs fw-600 text-slate-800 truncate mb-0">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}</p>
                            </div>
                            <div class="col-6">
                                <p class="text-xxs text-slate-400 mb-0">Tanggal</p>
                                <p class="text-xs fw-600 text-slate-800 mb-0">{{ $transportRequest->tanggal->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        @endif

        <p class="text-center text-xxs text-slate-400 mt-3 mb-0">rs azra &bull; Sistem Pengajuan Transportasi</p>
    </div>
</body>
</html>
