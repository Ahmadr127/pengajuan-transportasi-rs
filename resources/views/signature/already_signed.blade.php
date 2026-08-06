<x-app-layout title="Sudah Ditandatangani — SIPETRANS">
    <div class="mx-auto px-3 py-4" style="max-width:28rem;">
        <div class="sp-card overflow-hidden">
            <div class="px-4 py-4 text-center" style="background-color:#475569;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:3rem; height:3rem; background-color:rgba(255,255,255,.2);">
                    <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="fw-bold text-white mb-1" style="font-size:1rem;">Sudah Ditandatangani</h1>
                <p class="text-xs mb-0" style="color:#e2e8f0;">Pengajuan {{ $transportRequest->nomor_pengajuan }}</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-sm text-slate-600 mb-3">Tidak ada tanda tangan yang diperlukan untuk pengajuan ini saat ini.</p>
                <a href="{{ auth()->user()->isAdmin() ? route('admin.transport.show', $transportRequest) : route('pengajuan.index') }}"
                   class="btn btn-sp-primary d-inline-flex align-items-center justify-content-center gap-2 px-4 py-2 text-sm fw-600">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
