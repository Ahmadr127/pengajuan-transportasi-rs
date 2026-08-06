@props(['title' => config('app.name', 'SIPETRANS')])
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-tab.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-tab.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important
        }

        /* Cegah flash sidebar saat page load */
        .sp-sidebar-no-transition {
            transition: none !important;
        }

        /* UI/UX Enhancements for Menus and Buttons */
        .sp-sidebar-link {
            transition: all 0.2s ease;
        }

        .sp-sidebar-link:hover {
            background-color: rgba(0, 119, 116, 0.06);
            color: #007774;
            transform: translateX(4px);
        }

        .sp-sidebar-link:hover .sp-sidebar-icon {
            background-color: rgba(0, 119, 116, 0.12);
            color: #007774;
        }

        .sp-sidebar-link:hover .sp-sidebar-icon svg {
            stroke: #007774;
        }

        .sp-user-btn {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .sp-user-btn:hover {
            transform: scale(1.05);
        }

        .sp-nav-btn {
            transition: transform 0.2s ease, background-color 0.2s ease;
            border-radius: 50%;
        }

        .sp-nav-btn:hover {
            transform: scale(1.05);
            background-color: rgba(0, 119, 116, 0.06);
        }

        .sp-dropdown-item {
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: #334155;
            text-decoration: none;
        }

        .sp-dropdown-item:hover {
            background-color: rgba(0, 119, 116, 0.06);
            color: #0f172a;
            transform: translateX(4px);
        }

        .sp-dropdown-item.text-danger:hover {
            background-color: rgba(239, 68, 68, 0.08);
            color: #dc2626 !important;
        }

        .sp-btn-hover {
            transition: all 0.2s ease;
        }

        .sp-btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .sp-header-btn-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sp-header-btn-hover:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            filter: brightness(1.05);
        }

        .sp-header-btn-hover:active {
            transform: translateY(0) scale(0.95) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
    </style>
    <script>
        // Nonaktifkan transition sidebar saat page load agar tidak flash
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sp-sidebar').forEach(function (el) {
                el.classList.add('sp-sidebar-no-transition');
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        el.classList.remove('sp-sidebar-no-transition');
                    });
                });
            });
        });
    </script>
</head>

<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">
    <div class="d-flex flex-column min-vh-100">

        {{-- Header --}}
        <header class="sp-navbar">
            <div class="px-3 py-3 d-flex align-items-center justify-content-between gap-3">

                @if(auth()->user()->isAdmin())
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="sp-nav-btn d-lg-none shrink-0 sp-header-btn-hover">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif

                @php
                    $logoHref = auth()->user()->isDriver()
                        ? route('driver.dashboard')
                        : (auth()->user()->isAdmin() ? route('admin.transport.index') : route('home'));
                @endphp
                <a href="{{ $logoHref }}"
                    class="d-flex align-items-center gap-2 min-w-0 text-decoration-none {{ auth()->user()->isAdmin() ? 'd-lg-none' : '' }}">
                    <div class="rounded p-1">
                        <img src="{{ asset('images/logo.png') }}" alt="RS Azra" style="height:2.5rem;width:auto;">
                    </div>
                    <div>
                        <div class="fw-bold tracking-wide" style="color:#007774;font-size:1rem;">SIPETRANS</div>
                        <div class="text-xxs fw-500" style="color:#81BD41;">Sistem Pengajuan Transportasi</div>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-3 ms-auto">

                    @if(auth()->user()->isAdmin())
                        {{-- Admin Notification --}}
                        @php
                            $pendingRequests = \App\Models\TransportRequest::where('status', 'diajukan')->latest()->limit(5)->get();
                            $pendingCount = $pendingRequests->count();
                            $todayReminders = \App\Models\TransportRequest::with(['user', 'driver'])->where('status', 'diproses')->whereDate('tanggal', today())->orderBy('jam', 'asc')->limit(5)->get();
                            $reminderCount = $todayReminders->count();
                            $totalNotifications = $pendingCount + $reminderCount;
                        @endphp
                        <div class="position-relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="sp-nav-btn position-relative sp-header-btn-hover">
                                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($totalNotifications > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center text-xxs fw-bold text-white bg-warning rounded-circle"
                                        style="width:1.25rem;height:1.25rem;margin-top:-.25rem;margin-left:-.25rem;">
                                        {{ $totalNotifications > 9 ? '9+' : $totalNotifications }}
                                    </span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="sp-dropdown z-50" style="width:20rem;display:none;">
                                @if($reminderCount > 0)
                                    <div
                                        class="px-3 py-2 bg-blue-50 border-bottom border-blue-200 d-flex align-items-center justify-content-between">
                                        <h3 class="text-sm fw-600 text-blue-900 mb-0">Reminder Hari Ini</h3>
                                        <a href="{{ route('admin.transport.index', ['status' => 'diproses']) }}"
                                            class="text-xxs fw-500 text-blue-700">Lihat Semua</a>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto border-bottom border-slate-200">
                                        @foreach($todayReminders as $reminder)
                                            <a href="{{ route('admin.transport.show', $reminder) }}"
                                                class="d-block px-3 py-2 border-bottom border-slate-100 text-decoration-none">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#3b82f6,#06b6d4);">
                                                        <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <p class="text-sm text-slate-900 fw-600 mb-0">
                                                            {{ $reminder->user->full_name ?? $reminder->pemohon_nama }}</p>
                                                        <p class="text-xs text-slate-600 mb-0 mt-1">
                                                            {{ ucfirst($reminder->jenis) }} • {{ $reminder->jam }}
                                                            @if($reminder->prioritas === 'segera')<span class="badge badge-red ms-1"
                                                            style="font-size:.5625rem;">CITO</span>@endif
                                                        </p>
                                                        <p class="text-xs text-slate-500 mb-0 mt-1">
                                                            {{ $reminder->unit_mobil ?? 'Menunggu unit' }}@if($reminder->driver) •
                                                            {{ $reminder->driver->name }}@endif</p>
                                                    </div>
                                                    <span class="badge badge-blue shrink-0"
                                                        style="font-size:.625rem;">Disetujui</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                                <div
                                    class="px-3 py-2 border-bottom border-slate-200 d-flex align-items-center justify-content-between">
                                    <h3 class="text-sm fw-600 text-slate-800 mb-0">Pengajuan Baru</h3>
                                    @if($pendingCount > 0)<a
                                        href="{{ route('admin.transport.index', ['status' => 'diajukan']) }}"
                                    class="text-xxs fw-500 text-emerald-600">Lihat Semua</a>@endif
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse($pendingRequests as $request)
                                        <div class="px-3 py-2 border-bottom border-slate-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                    style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#f59e0b,#ea580c);">
                                                    <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <p class="text-sm text-slate-900 fw-600 mb-0">
                                                        {{ $request->user->full_name ?? $request->pemohon_nama }}</p>
                                                    <p class="text-xs text-slate-600 mb-0 mt-1">Mengajukan
                                                        {{ ucfirst($request->jenis) }}@if($request->prioritas === 'segera')<span
                                                            class="badge badge-red ms-1"
                                                        style="font-size:.5625rem;">CITO</span>@endif</p>
                                                    <p class="text-xs text-slate-500 mb-0 mt-1">
                                                        {{ $request->tanggal->format('d M Y') }} •
                                                        {{ $request->user->unit_kerja ?? $request->pemohon_unit }}</p>
                                                    <p class="text-xxs text-slate-400 mb-0 mt-1">
                                                        {{ $request->created_at->diffForHumans() }}</p>
                                                </div>
                                                <span class="badge badge-amber shrink-0" style="font-size:.625rem;">Baru</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-3 py-4 text-center">
                                            <svg class="text-slate-400 mx-auto mb-2" style="width:3rem;height:3rem;" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm text-slate-500 fw-500 mb-0">Tidak ada pengajuan baru</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!auth()->user()->isAdmin() && !auth()->user()->isDriver())
                        {{-- User Notification --}}
                        @php
                            $approvedRequests = \App\Models\TransportRequest::where('user_id', auth()->id())->whereIn('status', ['diproses', 'digunakan'])->whereDate('updated_at', today())->latest()->limit(5)->get();
                            $approvedCount = $approvedRequests->count();
                        @endphp
                        <div class="position-relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="sp-nav-btn position-relative sp-header-btn-hover">
                                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if($approvedCount > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center text-xxs fw-bold text-white bg-danger rounded-circle"
                                        style="width:1.25rem;height:1.25rem;margin-top:-.25rem;margin-left:-.25rem;">
                                        {{ $approvedCount > 9 ? '9+' : $approvedCount }}
                                    </span>
                                @endif
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="sp-dropdown z-50" style="width:20rem;display:none;">
                                <div
                                    class="px-3 py-2 border-bottom border-slate-200 d-flex align-items-center justify-content-between">
                                    <h3 class="text-sm fw-600 text-slate-800 mb-0">Notifikasi</h3>
                                    @if($approvedCount > 0)<a href="{{ route('pengajuan.index') }}"
                                    class="text-xxs fw-500 text-emerald-600">Lihat Semua</a>@endif
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    @forelse($approvedRequests as $request)
                                        <div class="px-3 py-2 border-bottom border-slate-100">
                                            <div class="d-flex align-items-start gap-2">
                                                @if($request->status === 'digunakan')
                                                    <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#06b6d4,#0d9488);">
                                                        <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#10b981,#0d9488);">
                                                        <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1 min-w-0">
                                                    <p class="text-sm text-slate-900 fw-500 mb-0">Pengajuan <span
                                                            class="fw-600">{{ ucfirst($request->jenis) }}</span>
                                                        {{ $request->status === 'digunakan' ? 'Sedang Digunakan' : 'Disetujui' }}
                                                    </p>
                                                    <p class="text-xs text-slate-500 mb-0 mt-1">
                                                        {{ $request->tanggal->format('d M Y') }} •
                                                        {{ $request->unit_mobil ?? 'Menunggu unit' }}</p>
                                                    <p class="text-xxs text-slate-400 mb-0 mt-1">
                                                        {{ $request->updated_at->diffForHumans() }}</p>
                                                </div>
                                                @if($request->status === 'digunakan')
                                                    <span class="badge badge-cyan shrink-0"
                                                        style="font-size:.625rem;">Digunakan</span>
                                                @else
                                                    <span class="badge badge-blue shrink-0"
                                                        style="font-size:.625rem;">Disetujui</span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-3 py-4 text-center">
                                            <p class="text-sm text-slate-500 fw-500 mb-0">Tidak ada notifikasi</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!auth()->user()->isAdmin())
                        {{-- Mobile User / Driver Dropdown (Garis 3) --}}
                        <div class="position-relative d-lg-none" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="sp-user-btn sp-header-btn-hover">
                                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="sp-dropdown z-50 overflow-hidden" style="width:14rem;display:none;">
                                <div class="px-3 py-2 border-bottom border-slate-100"
                                    style="background:rgba(0,119,116,0.04);">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                                            style="width:2.25rem;height:2.25rem;background-color:#007774;">
                                            <svg style="width:1rem;height:1rem;" class="text-white" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm fw-600 text-slate-800 truncate">
                                                {{ auth()->user()->full_name }}</div>
                                            @if(auth()->user()->isDriver())
                                                <div class="text-xs fw-500" style="color:#007774;">Driver</div>
                                            @else
                                                <div class="text-xs text-slate-500 truncate">
                                                    {{ auth()->user()->unit_kerja ?? auth()->user()->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <hr class="my-1" style="border-color:#f1f5f9;">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="sp-dropdown-item text-danger rounded">
                                            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Desktop User / Driver Navigation (Web Mode) --}}
                        <div class="d-none d-lg-flex align-items-center gap-3">
                            {{-- User Name / Info --}}
                            <div class="d-flex align-items-center gap-2 px-3 rounded"
                                style="height:2.5rem; background:rgba(0,119,116,0.06); border:1px solid rgba(0,119,116,0.1);">
                                <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                                    style="width:1.75rem;height:1.75rem;background-color:#007774;">
                                    <svg style="width:0.875rem;height:0.875rem;" class="text-white" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs fw-600 text-slate-800" style="line-height:1.2;">
                                        {{ auth()->user()->full_name }}</div>
                                    <div class="text-xxs text-slate-500" style="line-height:1.2;">
                                        {{ auth()->user()->isDriver() ? 'Driver' : (auth()->user()->unit_kerja ?? auth()->user()->email) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Riwayat Button --}}
                            @php
                                $historyRoute = auth()->user()->isDriver() ? route('driver.history') : route('pengajuan.index');
                            @endphp
                            <a href="{{ $historyRoute }}"
                                class="sp-admin-logout-btn sp-header-btn-hover d-flex align-items-center gap-2 px-3 text-xs fw-500 rounded text-decoration-none"
                                style="height:2rem;">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M6 12h12M6 17h8" />
                                </svg>
                                Riwayat
                            </a>

                            {{-- Keluar Button --}}
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="btn d-flex align-items-center gap-2 px-3 text-xs fw-500 rounded text-decoration-none text-white sp-header-btn-hover"
                                    style="height:2rem; background:#ef4444;border:1px solid #ef4444;transition:all 0.2s;"
                                    onmouseover="this.style.background='#dc2626'; this.style.borderColor='#dc2626';"
                                    onmouseout="this.style.background='#ef4444'; this.style.borderColor='#ef4444';">
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="sp-admin-logout-btn sp-header-btn-hover d-flex align-items-center gap-2 px-3 py-1 text-xs fw-500 rounded">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="d-none d-sm-inline">Keluar</span>
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </header>

        <div class="d-flex flex-grow-1 position-relative">

            {{-- Sidebar Overlay (mobile) --}}
            @if(!auth()->user()->isDriver())
                <div x-show="sidebarOpen" @click="sidebarOpen = false"
                    x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="sp-sidebar-overlay d-lg-none" style="display:none;"></div>
            @endif

            {{-- Sidebar --}}
            @if(!auth()->user()->isDriver())
                <aside :class="sidebarOpen ? '' : 'sp-sidebar-hidden'"
                    class="sp-sidebar {{ auth()->user()->isAdmin() ? 'sp-sidebar-lg-show' : '' }}">

                    {{-- Sidebar Header / Logo --}}
                    <div class="px-3 pt-4 pb-3" style="border-bottom:1px solid #e8f0ef;">
                        <div class="d-flex align-items-center gap-3 mb-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="shrink-0"
                                style="height:3rem;width:auto;object-fit:contain;">
                            <div class="fw-bold tracking-wide text-uppercase" style="color:#007774;font-size:1rem;">
                                SiPetrans</div>
                        </div>
                        <div class="text-sm fw-500 text-slate-500 mb-3">Sistem Pengajuan Transportasi</div>
                        <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
                            style="background:rgba(0,119,116,0.04);">
                            <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                                style="width:2.25rem;height:2.25rem;background-color:#007774;">
                                <svg style="width:1rem;height:1rem;" class="text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm fw-600 text-slate-800 truncate">{{ auth()->user()->full_name }}</div>
                                @if(auth()->user()->isAdmin())
                                    <div class="text-xs fw-500" style="color:#007774;">Administrator</div>
                                @else
                                    <div class="text-xs text-slate-500 truncate">
                                        {{ auth()->user()->unit_kerja ?? auth()->user()->email }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar Navigation --}}
                    <nav class="px-3 py-3">
                        @if(auth()->user()->isAdmin())

                            {{-- Admin Nav --}}
                            <a href="{{ route('admin.dashboard') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7" />
                                        <rect x="14" y="3" width="7" height="7" />
                                        <rect x="14" y="14" width="7" height="7" />
                                        <rect x="3" y="14" width="7" height="7" />
                                    </svg>
                                </span>
                                <span>Dashboard</span>
                            </a>

                            <a href="{{ route('admin.transport.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.transport.*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <span>Daftar Pengajuan</span>
                            </a>

                            <a href="{{ route('admin.laporan') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </span>
                                <span>Laporan</span>
                            </a>

                            <div class="sp-sidebar-section">Master Data</div>

                            <a href="{{ route('admin.users.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                </span>
                                <span>User / Akun</span>
                            </a>

                            <a href="{{ route('admin.vehicles.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4h13l3 5v4H3V4z" />
                                    </svg>
                                </span>
                                <span>Kendaraan</span>
                            </a>

                            <a href="{{ route('admin.drivers.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="4" />
                                        <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <span>Driver</span>
                            </a>

                            <a href="{{ route('admin.recurring-templates.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('admin.recurring-templates.*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </span>
                                <span>Pengajuan Berulang</span>
                            </a>

                        @else
                            {{-- User Nav --}}
                            <a href="{{ route('home') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('home') || request()->routeIs('pengajuan.choose') || request()->routeIs('pengajuan.umum*') || request()->routeIs('pengajuan.ambulance*') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 11.5 12 5l8 6.5V20H4v-8.5Z" />
                                    </svg>
                                </span>
                                <span>Pengajuan</span>
                            </a>

                            <a href="{{ route('pengajuan.index') }}" @click="sidebarOpen = false"
                                class="sp-sidebar-link {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') || request()->routeIs('pengajuan.success') ? 'active' : '' }}">
                                <span class="sp-sidebar-icon">
                                    <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round">
                                        <path d="M6 7h12M6 12h12M6 17h8" />
                                    </svg>
                                </span>
                                <span>Riwayat</span>
                            </a>
                        @endif
                    </nav>
                </aside>
            @endif

            {{-- Driver Sidebar --}}
            @if(auth()->user()->isDriver())
                <aside :class="sidebarOpen ? '' : 'sp-sidebar-hidden'" class="sp-sidebar">
                    <div class="px-3 pt-4 pb-3" style="border-bottom:1px solid #e8f0ef;">
                        <div class="d-flex align-items-center gap-3 mb-1">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="shrink-0"
                                style="height:3rem;width:auto;object-fit:contain;">
                            <div class="fw-bold tracking-wide text-uppercase" style="color:#007774;font-size:1rem;">
                                SiPetrans</div>
                        </div>
                        <div class="text-sm fw-500 text-slate-500 mb-3">Sistem Pengajuan Transportasi</div>
                        <div class="d-flex align-items-center gap-3 px-3 py-2 rounded"
                            style="background:rgba(0,119,116,0.04);">
                            <div class="d-flex align-items-center justify-content-center rounded-circle shrink-0"
                                style="width:2.25rem;height:2.25rem;background-color:#007774;">
                                <svg style="width:1rem;height:1rem;" class="text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm fw-600 text-slate-800 truncate">{{ auth()->user()->full_name }}</div>
                                <div class="text-xs fw-500" style="color:#007774;">Driver</div>
                            </div>
                        </div>
                    </div>
                    <nav class="px-3 py-3">
                        <a href="{{ route('driver.dashboard') }}" @click="sidebarOpen = false"
                            class="sp-sidebar-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                            <span class="sp-sidebar-icon">
                                <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4" />
                                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                            </span>
                            <span>Tugas Saya</span>
                        </a>
                        <a href="{{ route('driver.history') }}" @click="sidebarOpen = false"
                            class="sp-sidebar-link {{ request()->routeIs('driver.history') ? 'active' : '' }}">
                            <span class="sp-sidebar-icon">
                                <svg style="width:1rem;height:1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round">
                                    <path d="M6 7h12M6 12h12M6 17h8" />
                                </svg>
                            </span>
                            <span>Riwayat</span>
                        </a>
                    </nav>
                </aside>
            @endif

            {{-- Main Content --}}
            <main
                class="flex-grow-1 min-w-0 px-3 px-sm-4 py-3 py-sm-4 bg-slate-50 {{ auth()->user()->isAdmin() ? 'sp-main-with-sidebar' : 'pb-mobile-nav' }}">
                <div class="container-xl px-0">
                    @if(session('status'))
                        <div class="mb-3 rounded border px-3 py-2 text-xs shadow-sm alert-sp-success">
                            {{ session('status') }}</div>
                    @endif
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

    {{-- Driver Bottom Navigation (Mobile) --}}
    @if(auth()->user()->isDriver())
        <nav class="driver-bottom-nav d-md-none">
            <a href="{{ route('driver.dashboard') }}"
                class="driver-nav-item {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m-9-5l7-4 7 4" />
                </svg>
                <span>Home</span>
            </a>
            <a href="{{ route('driver.history') }}"
                class="driver-nav-item {{ request()->routeIs('driver.history') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('driver.profil') }}"
                class="driver-nav-item {{ request()->routeIs('driver.profil') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    @endif

    {{-- User Bottom Navigation (Mobile) --}}
    @if(!auth()->user()->isAdmin() && !auth()->user()->isDriver())
        <nav class="user-bottom-nav d-md-none">
            <a href="{{ route('home') }}"
                class="user-nav-item {{ request()->routeIs('home') || request()->routeIs('pengajuan.choose') || request()->routeIs('pengajuan.umum*') || request()->routeIs('pengajuan.ambulance*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18H4V8h8V3l8 5v10h-8v5" />
                </svg>
                <span>Pengajuan</span>
            </a>
            <a href="{{ route('pengajuan.index') }}"
                class="user-nav-item {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') || request()->routeIs('pengajuan.success') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Riwayat</span>
            </a>
            <a href="{{ route('profil') }}" class="user-nav-item {{ request()->routeIs('profil') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    @endif

</body>

</html>

