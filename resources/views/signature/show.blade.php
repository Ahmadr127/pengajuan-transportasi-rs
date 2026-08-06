<x-app-layout title="Tanda Tangan Digital — SIPETRANS">
    <div class="mx-auto px-3 py-2" style="max-width:48rem;">
        <div class="sp-card p-3">
            <div class="text-center mb-3">
                <h1 class="fw-bold text-slate-800 mb-1" style="font-size:1.125rem;">Tanda Tangan Digital</h1>
                <p class="text-xs text-slate-600 mb-0">Scan QR Code untuk menandatangani dokumen</p>
            </div>

            <div class="bg-white rounded p-3 mb-3">
                <div class="text-center">
                    <div class="d-inline-block bg-white p-2 rounded shadow-sm mb-2">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(route('signature.confirm', $transportRequest)) }}"
                             alt="QR Code"
                             style="width:160px; height:160px;">
                    </div>
                    <p class="text-xs fw-600 text-slate-700 mb-1">
                        @if($signatureType === 'pemohon')
                            Tanda Tangan Pemohon
                        @elseif($signatureType === 'pengelola_1')
                            Tanda Tangan Pengelola (Menyetujui)
                        @elseif($signatureType === 'driver')
                            Tanda Tangan Pengemudi
                        @elseif($signatureType === 'pengelola_2')
                            Tanda Tangan Pengelola (Mengetahui)
                        @endif
                    </p>
                    <p class="text-xxs text-slate-500 mb-0">
                        Pengajuan {{ $transportRequest->nomor_pengajuan }}
                    </p>
                </div>
            </div>

            <div class="alert-sp-info border rounded p-2 mb-3">
                <div class="d-flex align-items-start gap-2">
                    <svg class="text-blue-600 shrink-0 mt-1" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-xxs leading-tight">
                        <p class="fw-600 mb-1">Cara Tanda Tangan:</p>
                        <ol class="ps-3 mb-0" style="line-height:1.6;">
                            <li>Scan QR Code menggunakan kamera smartphone</li>
                            <li>Atau klik tombol "Tanda Tangan Sekarang"</li>
                            <li>Tanda tangan akan tersimpan otomatis</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6 text-xxs">
                    <span class="text-slate-500">Jenis:</span>
                    <span class="fw-500 text-slate-900 ms-1">{{ ucfirst($transportRequest->jenis) }}</span>
                </div>
                <div class="col-6 text-xxs">
                    <span class="text-slate-500">Tanggal:</span>
                    <span class="fw-500 text-slate-900 ms-1">{{ $transportRequest->tanggal->format('d/m/Y') }}</span>
                </div>
                <div class="col-12 text-xxs">
                    <span class="text-slate-500">Pemohon:</span>
                    <span class="fw-500 text-slate-900 ms-1">{{ $transportRequest->user->full_name ?? $transportRequest->pemohon_nama }}</span>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <form method="POST" action="{{ route('signature.sign', $transportRequest) }}">
                        @csrf
                        <button type="submit"
                                class="btn btn-sp-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 text-xs fw-600 py-2">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tanda Tangan
                        </button>
                    </form>
                </div>
                <div class="col-6">
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.transport.show', $transportRequest) : route('pengajuan.index') }}"
                       class="btn btn-sp-outline w-100 d-inline-flex align-items-center justify-content-center gap-2 text-xs fw-600 py-2">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
