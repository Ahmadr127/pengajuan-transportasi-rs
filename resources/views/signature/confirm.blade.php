<x-app-layout title="Konfirmasi Tanda Tangan — SIPETRANS">
    <div class="mx-auto px-3 py-4" style="max-width:28rem;">
        <div class="sp-card overflow-hidden">

            <!-- Header -->
            <div class="px-4 py-4 text-center" style="background-color:#059669;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                     style="width:3rem; height:3rem; background-color:rgba(255,255,255,.2);">
                    <svg class="text-white" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <h1 class="fw-bold text-white mb-1" style="font-size:1rem;">Konfirmasi Tanda Tangan</h1>
                <p class="text-xs mb-0" style="color:#a7f3d0;">
                    @if($signatureType === 'pemohon') Tanda Tangan Pemohon
                    @elseif($signatureType === 'pengelola_1') Pengelola - Menyetujui
                    @elseif($signatureType === 'driver') Tanda Tangan Pengemudi
                    @elseif($signatureType === 'pengelola_2') Pengelola - Mengetahui
                    @endif
                </p>
            </div>

            <div class="p-4">
                <!-- Info Pengajuan -->
                <div class="bg-slate-50 rounded p-3 mb-3">
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-slate-500">No. Pengajuan</span>
                        <span class="font-mono fw-600 text-slate-800">{{ $transportRequest->nomor_pengajuan }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-slate-500">Jenis</span>
                        <span class="fw-600 text-slate-800">{{ ucfirst($transportRequest->jenis) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-slate-500">Pemohon</span>
                        <span class="fw-600 text-slate-800">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-xs mb-2">
                        <span class="text-slate-500">Tanggal</span>
                        <span class="fw-600 text-slate-800">{{ $transportRequest->tanggal->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-xs">
                        <span class="text-slate-500">Tujuan</span>
                        <span class="fw-600 text-slate-800 text-end" style="max-width:60%;">{{ $transportRequest->alamat_tujuan ?? '-' }}</span>
                    </div>
                </div>

                <!-- Peringatan -->
                <div class="alert-sp-warning border rounded p-3 d-flex gap-2 mb-3">
                    <svg class="text-amber-600 shrink-0 mt-1" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs mb-0">
                        Dengan menandatangani, Anda mengonfirmasi kebenaran data pengajuan ini. Tanda tangan tidak dapat dibatalkan.
                    </p>
                </div>

                <!-- Penanda tangan -->
                <div class="bg-emerald-50 border border-emerald-200 rounded p-3 d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                         style="width:2.25rem; height:2.25rem; background-color:#059669;">
                        <svg class="text-white" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs fw-600 text-emerald-900 mb-0">{{ auth()->user()->full_name }}</p>
                        <p class="text-xxs text-emerald-700 mb-0">{{ auth()->user()->unit_kerja ?? 'Pengelola Transportasi' }}</p>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="row g-2">
                    <div class="col-6">
                        <form method="POST" action="{{ route('signature.sign', $transportRequest) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sp-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 text-sm fw-600 py-2">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Tanda Tangan
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.transport.show', $transportRequest) : route('pengajuan.index') }}"
                           class="btn btn-sp-outline w-100 d-inline-flex align-items-center justify-content-center gap-2 text-sm fw-600 py-2">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
