<x-app-layout title="Tidak Ada Akses — SIPETRANS">
    <div class="px-3 py-5" style="max-width:32rem;margin:0 auto;">
        <div class="sp-card p-4 text-center">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                 style="width:3.5rem;height:3.5rem;background:rgba(0,119,116,0.08);">
                <svg style="width:1.75rem;height:1.75rem;color:#007774;" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-sm fw-bold text-slate-800 mb-2">Belum Ada Akses</h1>
            <p class="text-xs text-slate-500 mb-3">
                Akun Anda belum memiliki role atau permission yang aktif. Hubungi administrator untuk
                mendapatkan akses.
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sp-primary text-xs fw-600">Keluar</button>
            </form>
        </div>
    </div>
</x-app-layout>
