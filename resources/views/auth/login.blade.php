<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — SIPETRANS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
</head>
<body class="d-flex align-items-center justify-content-center position-relative overflow-hidden" style="min-height:100vh;">

    {{-- Background blur --}}
    <div class="position-absolute inset-0" style="z-index:-1; background: url('{{ asset('images/login-bg.svg') }}') center center / cover no-repeat; filter: blur(6px); transform: scale(1.05);"></div>

    {{-- Login Card --}}
    <div class="position-relative w-100 mx-3" style="z-index:10; max-width:48rem;">
        <div class="d-flex rounded overflow-hidden" style="min-height:360px; box-shadow: 0 8px 40px rgba(0,0,0,0.18), 0 2px 8px rgba(0,0,0,0.10);">

            {{-- Kiri: Branding --}}
            <div class="d-none d-sm-flex flex-column align-items-center justify-content-center px-4 py-5 sp-login-brand" style="width:40%; background-color:white;">
                <div class="d-flex flex-column align-items-center text-center gap-3">
                    <div class="rounded p-3">
                        <img src="{{ asset('images/logo.png') }}" alt="RS Azra" style="height:5rem; width:auto;">
                    </div>
                    <div>
                        <div class="fw-bold tracking-widest text-uppercase mb-2" style="font-size:1.125rem; color:#6DB33F;">SIPETRANS</div>
                        <div class="fw-bold leading-snug" style="font-size:1.125rem; color:#007774;">
                            Sistem Pengajuan<br>Transportasi
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Form --}}
            <div class="flex-grow-1 bg-white d-flex flex-column justify-content-center px-4 py-5 position-relative">

                {{-- Mobile logo --}}
                <div class="d-flex d-sm-none align-items-center gap-3 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="RS Azra" style="height:2.25rem; width:auto;">
                    <div>
                        <div class="fw-bold tracking-widest text-uppercase text-xxs" style="color:#6DB33F;">SIPETRANS</div>
                        <div class="text-xxs text-slate-500">Sistem Pengajuan Transportasi</div>
                    </div>
                </div>

                <h2 class="fw-bold text-slate-800 mb-4" style="font-size:1.25rem;">Selamat Datang!</h2>

                <form method="POST" action="{{ route('login.store') }}" x-data="{ showPass: false }">
                    @csrf

                    {{-- Username --}}
                    <div class="mb-3">
                        <label for="username" class="form-label text-xs fw-600 text-slate-700 mb-1">Username</label>
                        <div class="position-relative">
                            <span class="position-absolute top-50 translate-middle-y ps-3 pointer-events-none" style="left:0;">
                                <svg class="text-slate-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                value="{{ old('username') }}"
                                required autofocus autocomplete="username"
                                placeholder="Masukan username"
                                class="form-control text-sm ps-5 @error('username') is-invalid @enderror"
                            >
                        </div>
                        @error('username')
                            <div class="text-xxs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label text-xs fw-600 text-slate-700 mb-1">Password</label>
                        <div class="position-relative">
                            <span class="position-absolute top-50 translate-middle-y ps-3 pointer-events-none" style="left:0;">
                                <svg class="text-slate-400" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                name="password"
                                :type="showPass ? 'text' : 'password'"
                                required
                                placeholder="Password"
                                class="form-control text-sm ps-5 pe-5 @error('password') is-invalid @enderror"
                            >
                            <button type="button" @click="showPass = !showPass"
                                    class="position-absolute top-50 translate-middle-y pe-3 d-flex align-items-center text-slate-400 border-0 bg-transparent"
                                    style="right:0;">
                                <svg x-show="!showPass" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showPass" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-xxs text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="btn w-100 text-sm fw-600 text-white mt-2"
                            style="background-color:#00685E; padding-top:.625rem; padding-bottom:.625rem;">
                        Login
                    </button>
                </form>

                <p class="text-center text-xxs text-slate-400 mt-4">© {{ date('Y') }} RS Azra</p>
            </div>

        </div>
    </div>

</body>
</html>
