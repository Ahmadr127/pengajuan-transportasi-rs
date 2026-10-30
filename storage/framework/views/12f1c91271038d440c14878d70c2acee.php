<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => config('app.name', 'SIPETRANS')]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title' => config('app.name', 'SIPETRANS')]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $user = auth()->user();
    $menuService = app(\App\Services\MenuService::class);
    $context = $menuService->contextFor($user);
    $sidebarMenu = $menuService->sidebar($user, $context);
    $bottomMenu = $menuService->bottomNav($user, $context);
    $landingRoute = $menuService->landingRoute($user);
    $adminContext = $context === 'admin';
    $historyRoute = $menuService->routeFor($user, 'driver.history')
        ?? $menuService->routeFor($user, 'user.transport.history');
    $useSidebar = $user->isSuperAdmin() || $user->hasPermission('layout.sidebar');
    $useTopNav = $user->hasPermission('layout.topnav') && ! $useSidebar;
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo-tab.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/logo-tab.png')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
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

        
        <header class="sp-navbar">
            <div class="px-3 py-3 d-flex align-items-center justify-content-between gap-3">

                <?php if($useSidebar && $sidebarMenu->isNotEmpty()): ?>
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="sp-nav-btn d-lg-none shrink-0 sp-header-btn-hover">
                        <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                <?php endif; ?>

                <a href="<?php echo e($landingRoute ? route($landingRoute) : url('/')); ?>"
                    class="d-flex align-items-center gap-2 min-w-0 text-decoration-none <?php echo e($adminContext ? 'd-lg-none' : ''); ?>">
                    <div class="rounded p-1">
                        <img src="<?php echo e(asset('images/logo.png')); ?>" alt="RS Azra" style="height:2.5rem;width:auto;">
                    </div>
                    <div>
                        <div class="fw-bold tracking-wide" style="color:#007774;font-size:1rem;">SIPETRANS</div>
                        <div class="text-xxs fw-500" style="color:#81BD41;">Sistem Pengajuan Transportasi</div>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-3 ms-auto">

                    <?php if($adminContext): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.admin.view')): ?>
                        
                        <?php
                            $pendingRequests = \App\Models\TransportRequest::where('status', 'diajukan')->latest()->limit(5)->get();
                            $pendingCount = $pendingRequests->count();
                            $todayReminders = \App\Models\TransportRequest::with(['user', 'driver'])->where('status', 'diproses')->whereDate('tanggal', today())->orderBy('jam', 'asc')->limit(5)->get();
                            $reminderCount = $todayReminders->count();
                            $totalNotifications = $pendingCount + $reminderCount;
                        ?>
                        <div class="position-relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="sp-nav-btn position-relative sp-header-btn-hover">
                                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <?php if($totalNotifications > 0): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center text-xxs fw-bold text-white bg-warning rounded-circle"
                                        style="width:1.25rem;height:1.25rem;margin-top:-.25rem;margin-left:-.25rem;">
                                        <?php echo e($totalNotifications > 9 ? '9+' : $totalNotifications); ?>

                                    </span>
                                <?php endif; ?>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="sp-dropdown z-50" style="width:20rem;display:none;">
                                <?php if($reminderCount > 0): ?>
                                    <div
                                        class="px-3 py-2 bg-blue-50 border-bottom border-blue-200 d-flex align-items-center justify-content-between">
                                        <h3 class="text-sm fw-600 text-blue-900 mb-0">Reminder Hari Ini</h3>
                                        <a href="<?php echo e(route('admin.transport.index', ['status' => 'diproses'])); ?>"
                                            class="text-xxs fw-500 text-blue-700">Lihat Semua</a>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto border-bottom border-slate-200">
                                        <?php $__currentLoopData = $todayReminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reminder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('admin.transport.show', $reminder)); ?>"
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
                                                            <?php echo e($reminder->user->full_name ?? $reminder->pemohon_nama); ?></p>
                                                        <p class="text-xs text-slate-600 mb-0 mt-1">
                                                            <?php echo e(ucfirst($reminder->jenis)); ?> • <?php echo e($reminder->jam); ?>

                                                            <?php if($reminder->prioritas === 'segera'): ?><span class="badge badge-red ms-1"
                                                            style="font-size:.5625rem;">CITO</span><?php endif; ?>
                                                        </p>
                                                        <p class="text-xs text-slate-500 mb-0 mt-1">
                                                            <?php echo e($reminder->unit_mobil ?? 'Menunggu unit'); ?><?php if($reminder->driver): ?> •
                                                            <?php echo e($reminder->driver->name); ?><?php endif; ?></p>
                                                    </div>
                                                    <span class="badge badge-blue shrink-0"
                                                        style="font-size:.625rem;">Disetujui</span>
                                                </div>
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                                <div
                                    class="px-3 py-2 border-bottom border-slate-200 d-flex align-items-center justify-content-between">
                                    <h3 class="text-sm fw-600 text-slate-800 mb-0">Pengajuan Baru</h3>
                                    <?php if($pendingCount > 0): ?><a
                                        href="<?php echo e(route('admin.transport.index', ['status' => 'diajukan'])); ?>"
                                    class="text-xxs fw-500 text-emerald-600">Lihat Semua</a><?php endif; ?>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <?php $__empty_1 = true; $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                                                        <?php echo e($request->user->full_name ?? $request->pemohon_nama); ?></p>
                                                    <p class="text-xs text-slate-600 mb-0 mt-1">Mengajukan
                                                        <?php echo e(ucfirst($request->jenis)); ?><?php if($request->prioritas === 'segera'): ?><span
                                                            class="badge badge-red ms-1"
                                                        style="font-size:.5625rem;">CITO</span><?php endif; ?></p>
                                                    <p class="text-xs text-slate-500 mb-0 mt-1">
                                                        <?php echo e($request->tanggal->format('d M Y')); ?> •
                                                        <?php echo e($request->user->unit_kerja ?? $request->pemohon_unit); ?></p>
                                                    <p class="text-xxs text-slate-400 mb-0 mt-1">
                                                        <?php echo e($request->created_at->diffForHumans()); ?></p>
                                                </div>
                                                <span class="badge badge-amber shrink-0" style="font-size:.625rem;">Baru</span>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="px-3 py-4 text-center">
                                            <svg class="text-slate-400 mx-auto mb-2" style="width:3rem;height:3rem;" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-sm text-slate-500 fw-500 mb-0">Tidak ada pengajuan baru</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.user.view')): ?>
                        
                        <?php
                            $approvedRequests = \App\Models\TransportRequest::where('user_id', auth()->id())->whereIn('status', ['diproses', 'digunakan'])->whereDate('updated_at', today())->latest()->limit(5)->get();
                            $approvedCount = $approvedRequests->count();
                        ?>
                        <div class="position-relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="sp-nav-btn position-relative sp-header-btn-hover">
                                <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <?php if($approvedCount > 0): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center text-xxs fw-bold text-white bg-danger rounded-circle"
                                        style="width:1.25rem;height:1.25rem;margin-top:-.25rem;margin-left:-.25rem;">
                                        <?php echo e($approvedCount > 9 ? '9+' : $approvedCount); ?>

                                    </span>
                                <?php endif; ?>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="sp-dropdown z-50" style="width:20rem;display:none;">
                                <div
                                    class="px-3 py-2 border-bottom border-slate-200 d-flex align-items-center justify-content-between">
                                    <h3 class="text-sm fw-600 text-slate-800 mb-0">Notifikasi</h3>
                                    <?php if($approvedCount > 0 && $historyRoute): ?><a href="<?php echo e(route($historyRoute)); ?>"
                                    class="text-xxs fw-500 text-emerald-600">Lihat Semua</a><?php endif; ?>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <?php $__empty_1 = true; $__currentLoopData = $approvedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="px-3 py-2 border-bottom border-slate-100">
                                            <div class="d-flex align-items-start gap-2">
                                                <?php if($request->status === 'digunakan'): ?>
                                                    <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#06b6d4,#0d9488);">
                                                        <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                        </svg>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width:2.5rem;height:2.5rem;background:linear-gradient(135deg,#10b981,#0d9488);">
                                                        <svg style="width:1.25rem;height:1.25rem;" class="text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1 min-w-0">
                                                    <p class="text-sm text-slate-900 fw-500 mb-0">Pengajuan <span
                                                            class="fw-600"><?php echo e(ucfirst($request->jenis)); ?></span>
                                                        <?php echo e($request->status === 'digunakan' ? 'Sedang Digunakan' : 'Disetujui'); ?>

                                                    </p>
                                                    <p class="text-xs text-slate-500 mb-0 mt-1">
                                                        <?php echo e($request->tanggal->format('d M Y')); ?> •
                                                        <?php echo e($request->unit_mobil ?? 'Menunggu unit'); ?></p>
                                                    <p class="text-xxs text-slate-400 mb-0 mt-1">
                                                        <?php echo e($request->updated_at->diffForHumans()); ?></p>
                                                </div>
                                                <?php if($request->status === 'digunakan'): ?>
                                                    <span class="badge badge-cyan shrink-0"
                                                        style="font-size:.625rem;">Digunakan</span>
                                                <?php else: ?>
                                                    <span class="badge badge-blue shrink-0"
                                                        style="font-size:.625rem;">Disetujui</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="px-3 py-4 text-center">
                                            <p class="text-sm text-slate-500 fw-500 mb-0">Tidak ada notifikasi</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(! $adminContext): ?>
                        
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
                                                <?php echo e($user->full_name); ?></div>
                                            <div class="text-xs fw-500" style="color:#007774;">
                                                <?php echo e($user->primaryRoleLabel()); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <hr class="my-1" style="border-color:#f1f5f9;">
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
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

                        
                        <div class="d-none d-lg-flex align-items-center gap-3">
                            
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
                                        <?php echo e($user->full_name); ?></div>
                                    <div class="text-xxs text-slate-500" style="line-height:1.2;">
                                        <?php echo e($user->primaryRoleLabel()); ?> · <?php echo e($user->unit_kerja ?? $user->username); ?>

                                    </div>
                                </div>
                            </div>

                            
                            <?php if($historyRoute): ?>
                                <a href="<?php echo e(route($historyRoute)); ?>"
                                    class="sp-admin-logout-btn sp-header-btn-hover d-flex align-items-center gap-2 px-3 text-xs fw-500 rounded text-decoration-none"
                                    style="height:2rem;">
                                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M6 12h12M6 17h8" />
                                    </svg>
                                    Riwayat
                                </a>
                            <?php endif; ?>

                            
                            <form method="POST" action="<?php echo e(route('logout')); ?>" class="m-0">
                                <?php echo csrf_field(); ?>
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
                    <?php endif; ?>

                    <?php if($adminContext): ?>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                class="sp-admin-logout-btn sp-header-btn-hover d-flex align-items-center gap-2 px-3 py-1 text-xs fw-500 rounded">
                                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="d-none d-sm-inline">Keluar</span>
                            </button>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </header>

        <div class="d-flex flex-grow-1 position-relative">

            
            <?php if($useSidebar && $sidebarMenu->isNotEmpty()): ?>
                <div x-show="sidebarOpen" @click="sidebarOpen = false"
                    x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="sp-sidebar-overlay d-lg-none" style="display:none;"></div>
            <?php endif; ?>

            
            <?php if($useSidebar && $sidebarMenu->isNotEmpty()): ?>
                <aside :class="sidebarOpen ? '' : 'sp-sidebar-hidden'"
                    class="sp-sidebar <?php echo e($adminContext ? 'sp-sidebar-lg-show' : ''); ?>">

                    
                    <div class="px-3 pt-4 pb-3" style="border-bottom:1px solid #e8f0ef;">
                        <div class="d-flex align-items-center gap-3 mb-1">
                            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo" class="shrink-0"
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
                                <div class="text-sm fw-600 text-slate-800 truncate"><?php echo e($user->full_name); ?></div>
                                <div class="text-xs fw-500" style="color:#007774;"><?php echo e($user->primaryRoleLabel()); ?></div>
                            </div>
                        </div>
                    </div>

                    
                    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => ['items' => $sidebarMenu]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sidebarMenu)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
                </aside>
            <?php endif; ?>

            
            <main
                class="flex-grow-1 min-w-0 px-3 px-sm-4 py-3 py-sm-4 bg-slate-50 <?php echo e($adminContext ? 'sp-main-with-sidebar' : ($bottomMenu->isNotEmpty() ? 'pb-mobile-nav' : '')); ?>">
                <div class="container-xl px-0">
                    <?php if(session('status')): ?>
                        <div class="mb-3 rounded border px-3 py-2 text-xs shadow-sm alert-sp-success">
                            <?php echo e(session('status')); ?></div>
                    <?php endif; ?>
                    <?php echo e($slot); ?>

                </div>
            </main>

        </div>
    </div>

    
    <?php if($bottomMenu->isNotEmpty()): ?>
        <?php if (isset($component)) { $__componentOriginal91530f48093dbfe9d7d6cfefc4ce84c1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91530f48093dbfe9d7d6cfefc4ce84c1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.bottom-nav','data' => ['items' => $bottomMenu,'itemClass' => $context === 'driver' ? 'driver-nav-item' : 'user-nav-item','navClass' => $context === 'driver' ? 'driver-bottom-nav d-md-none' : 'user-bottom-nav d-md-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bottom-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bottomMenu),'item-class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context === 'driver' ? 'driver-nav-item' : 'user-nav-item'),'nav-class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($context === 'driver' ? 'driver-bottom-nav d-md-none' : 'user-bottom-nav d-md-none')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91530f48093dbfe9d7d6cfefc4ce84c1)): ?>
<?php $attributes = $__attributesOriginal91530f48093dbfe9d7d6cfefc4ce84c1; ?>
<?php unset($__attributesOriginal91530f48093dbfe9d7d6cfefc4ce84c1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91530f48093dbfe9d7d6cfefc4ce84c1)): ?>
<?php $component = $__componentOriginal91530f48093dbfe9d7d6cfefc4ce84c1; ?>
<?php unset($__componentOriginal91530f48093dbfe9d7d6cfefc4ce84c1); ?>
<?php endif; ?>
    <?php endif; ?>

</body>

</html>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/layouts/app.blade.php ENDPATH**/ ?>