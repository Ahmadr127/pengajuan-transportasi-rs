<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Transportasi RSAzra') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-slate-800 antialiased">

    <!-- Header Bar Accent -->
    <div class="h-0.5" style="background-color: #007774;"></div>
    
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="w-60 bg-white flex flex-col" style="border-right: 1px solid #e8f0ef;">

            <!-- Brand / Logo -->
            <div class="px-5 pt-6 pb-5" style="border-bottom: 1px solid #e8f0ef;">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                         style="background-color: #007774;">
                        RS
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-800 leading-tight">RS Azra</div>
                        <div class="text-xs text-slate-400 leading-tight">Transportasi</div>
                    </div>
                </div>
                
                <!-- User Profile -->
                <div class="flex items-center gap-3 px-3 py-3 rounded-lg" style="background: rgba(0,119,116,0.04);">
                    <div class="w-11 h-11 rounded-full flex items-center justify-center flex-shrink-0"
                         style="background-color: #007774;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-0.5">

                @if(request()->routeIs('admin.*'))
                    <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest" style="color: #007774;">Panel Admin</p>

                    <a href="{{ route('admin.transport.index') }}"
                       class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150
                              {{ request()->routeIs('admin.transport.*') ? 'sidebar-link-active' : '' }}">
                        <span class="sidebar-icon inline-flex w-7 h-7 items-center justify-center rounded-md transition-all duration-150">
                            <svg viewBox="0 0 24 24" class="w-4 h-4"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" class="fill-none stroke-current" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-medium">Daftar Pengajuan</span>
                    </a>

                    <div class="my-3" style="border-top: 1px solid #f0f4f4;"></div>
                    <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Lainnya</p>

                    <a href="{{ route('dashboard') }}"
                       class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150">
                        <span class="sidebar-icon inline-flex w-7 h-7 items-center justify-center rounded-md transition-all duration-150">
                            <svg viewBox="0 0 24 24" class="w-4 h-4"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" class="fill-none stroke-current" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-medium">Kembali ke User</span>
                    </a>

                @else
                    <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest" style="color: #007774;">Menu Utama</p>

                    <a href="{{ route('dashboard') }}"
                       class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150
                              {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                        <span class="sidebar-icon inline-flex w-7 h-7 items-center justify-center rounded-md transition-all duration-150">
                            <svg viewBox="0 0 24 24" class="w-4 h-4"><path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" class="fill-none stroke-current" stroke-width="1.6" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('pengajuan.index') }}"
                       class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150
                              {{ request()->routeIs('pengajuan.*') ? 'sidebar-link-active' : '' }}">
                        <span class="sidebar-icon inline-flex w-7 h-7 items-center justify-center rounded-md transition-all duration-150">
                            <svg viewBox="0 0 24 24" class="w-4 h-4"><path d="M6 7h12M6 12h12M6 17h8" class="fill-none stroke-current" stroke-width="1.6" stroke-linecap="round"/></svg>
                        </span>
                        <span class="font-medium">Riwayat Pengajuan</span>
                    </a>

                    @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="my-3" style="border-top: 1px solid #f0f4f4;"></div>
                    <p class="px-3 pt-1 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Administrator</p>

                    <a href="{{ route('admin.transport.index') }}"
                       class="sidebar-link group flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150
                              {{ request()->routeIs('admin.transport.*') ? 'sidebar-link-active' : '' }}">
                        <span class="sidebar-icon inline-flex w-7 h-7 items-center justify-center rounded-md transition-all duration-150">
                            <svg viewBox="0 0 24 24" class="w-4 h-4"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" class="fill-none stroke-current" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="font-medium">Panel Admin</span>
                    </a>
                    @endif
                @endif

            </nav>

            <!-- Profile + Logout -->
            <div class="px-4 py-4" style="border-top: 1px solid #e8f0ef;">
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium rounded-lg transition-all duration-150"
                        style="color: #007774; border: 1px solid #007774; background: white;"
                        onmouseover="this.style.background='#007774'; this.style.color='white';"
                        onmouseout="this.style.background='white'; this.style.color='#007774';">
                        <svg viewBox="0 0 24 24" class="w-3.5 h-3.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" class="fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Keluar
                    </button>
                </form>
            </div>

        </aside>

        <!-- Main Content -->
        <main class="flex-1 px-10 py-10 overflow-auto">
            <div class="max-w-6xl mx-auto">

                <!-- Status Alert -->
                @if (session('status'))
                    <div class="mb-6 rounded-2xl bg-emerald-50 
                                border border-emerald-200 
                                px-5 py-4 shadow-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5" 
                             viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                        </svg>
                        <p class="text-sm text-emerald-800">
                            {{ session('status') }}
                        </p>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-8">
                    {{ $slot }}
                </div>

            </div>
        </main>

    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    confirmButtonColor: '#007774',
                });
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: 'Terjadi Kesalahan!',
                    text: "{{ session('error') }}",
                    icon: 'error',
                    confirmButtonColor: '#007774',
                });
            });
        </script>
    @endif
</body>
</html>