<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Daftar Pengajuan — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daftar Pengajuan — SIPETRANS']); ?>
    <div class="container-fluid px-3 px-sm-4 pt-4 pt-sm-5 pb-4">
        <h1 class="fs-5 fw-bold text-slate-800">
            Daftar Pengajuan Transportasi
        </h1>
        <p class="text-slate-500 mt-1 text-xs">
            Kelola semua pengajuan mobil umum dan ambulance
        </p>

        <?php if(session('success')): ?>
            <div class="alert alert-sp-success mt-3 py-2 px-3 rounded fw-500 text-xs">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="mt-3 mt-sm-4">
            <!-- Filters -->
            <form method="GET" action="<?php echo e(route('admin.transport.index')); ?>" class="sp-card p-2 mb-3">
                <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
                    <select name="jenis" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="umum" <?php if(request('jenis') === 'umum'): echo 'selected'; endif; ?>>Mobil Umum</option>
                        <option value="ambulance" <?php if(request('jenis') === 'ambulance'): echo 'selected'; endif; ?>>Ambulance</option>
                    </select>

                    <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diajukan" <?php if(request('status') === 'diajukan'): echo 'selected'; endif; ?>>Menunggu (Diajukan)</option>
                        <option value="diproses" <?php if(request('status') === 'diproses'): echo 'selected'; endif; ?>>Disetujui</option>
                        <option value="digunakan" <?php if(request('status') === 'digunakan'): echo 'selected'; endif; ?>>Digunakan</option>
                        <option value="selesai" <?php if(request('status') === 'selesai'): echo 'selected'; endif; ?>>Selesai</option>
                        <option value="tidak_disetujui" <?php if(request('status') === 'tidak_disetujui'): echo 'selected'; endif; ?>>Tidak Disetujui</option>
                    </select>

                    <input type="date" name="tanggal" value="<?php echo e(request('tanggal')); ?>"
                           class="form-control form-control-sm" style="width:auto;"
                           onchange="this.form.submit()">

                    <?php if(request()->hasAny(['jenis', 'status', 'tanggal'])): ?>
                        <a href="<?php echo e(route('admin.transport.index')); ?>" class="btn btn-sm btn-outline-secondary px-3 py-1 text-xs fw-500">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Table List (Desktop) / Card List (Mobile) -->
            <div class="sp-card overflow-hidden">
                <!-- Desktop Table View -->
                <div class="d-none d-md-block overflow-x-auto">
                    <table class="sp-table w-100">
                        <thead>
                            <tr>
                                <th>No. Pengajuan</th>
                                <th>Pemohon</th>
                                <th>Jenis</th>
                                <th>Tanggal &amp; Waktu</th>
                                <th>Dibuat</th>
                                <th>Kendaraan</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('admin.transport.show', $item)); ?>"
                                           class="font-mono fw-700 text-teal-700 bg-teal-50 border border-teal-200 px-2 py-1 rounded d-inline-block text-decoration-none"
                                           style="font-size:.75rem;letter-spacing:.03em;">
                                            <?php echo e($item->nomor_pengajuan); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-600 text-xs text-slate-900"><?php echo e($item->user->full_name ?? $item->pemohon_nama); ?></div>
                                        <div class="text-xxs text-slate-500"><?php echo e($item->user->unit_kerja ?? $item->pemohon_unit); ?></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge status-badge <?php echo e($item->jenis === 'ambulance' ? 'bg-red-100 text-red-700 border border-red-200' : 'badge-emerald'); ?>">
                                                <?php echo e(ucfirst($item->jenis)); ?>

                                            </span>
                                            <?php if($item->prioritas === 'segera'): ?>
                                                <span class="badge status-badge badge-red">CITO</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-xs text-slate-700 fw-500 whitespace-nowrap"><?php echo e($item->tanggal->format('d/m/Y')); ?></div>
                                        <div class="text-xxs text-slate-500 whitespace-nowrap"><?php echo e(substr($item->jam, 0, 5)); ?>

                                            <?php if($item->jam_sampai): ?> - <?php echo e(substr($item->jam_sampai, 0, 5)); ?> <?php else: ?> - selesai <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-xs text-slate-700 fw-500 whitespace-nowrap"><?php echo e($item->created_at->format('d/m/Y')); ?></div>
                                        <div class="text-xxs text-slate-500 whitespace-nowrap"><?php echo e($item->created_at->format('H:i')); ?></div>
                                    </td>
                                    <td>
                                        <?php if($item->unit_mobil): ?>
                                            <div class="text-xs text-slate-700 fw-500 text-capitalize"><?php echo e(str_replace('_', ' ', $item->unit_mobil)); ?></div>
                                            <?php if($item->plat_nomor): ?>
                                                <div class="text-xxs text-slate-500 font-mono"><?php echo e($item->plat_nomor); ?></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-xxs text-slate-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->alamat_tujuan): ?>
                                            <div class="text-xs text-slate-600" style="max-width:16rem;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;"><?php echo e($item->alamat_tujuan); ?></div>
                                        <?php else: ?>
                                            <span class="text-xxs text-slate-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeMap = ['diajukan' => 'badge-amber', 'diproses' => 'badge-blue', 'digunakan' => 'badge-cyan', 'selesai' => 'badge-emerald', 'tidak_disetujui' => 'badge-red'];
                                            $badgeClass = $badgeMap[$item->status] ?? 'badge-slate';
                                            $label = match($item->status) {
                                                'diproses' => 'Disetujui',
                                                'digunakan' => 'Digunakan',
                                                default => ucfirst($item->status)
                                            };
                                        ?>
                                        <span class="badge status-badge <?php echo e($badgeClass); ?> whitespace-nowrap">
                                            <?php echo e($label); ?>

                                        </span>
                                        <?php if($item->status === 'diajukan' && !$item->signature_pemohon): ?>
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        <?php elseif($item->status === 'diproses' && !$item->signature_pengelola_1): ?>
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        <?php elseif($item->status === 'digunakan' && !$item->signature_driver): ?>
                                            <div class="mt-1 text-xxs text-amber-600 fw-600 d-flex align-items-center gap-1">
                                                <svg style="width:.625rem;height:.625rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                                Belum TTD
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('admin.transport.show', $item)); ?>" class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1 whitespace-nowrap" style="font-size:.625rem;">
                                            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="white" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="px-3 py-5 text-center">
                                        <svg style="width:2.5rem;height:2.5rem;" class="text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-slate-500 text-xs fw-500 mb-0">Belum ada data pengajuan transportasi.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="d-md-none divide-y divide-slate-200">
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-3" style="transition:background .1s;">
                            <!-- Header -->
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-600 text-sm text-slate-900 truncate"><?php echo e($item->user->full_name ?? $item->pemohon_nama); ?></div>
                                    <div class="text-xs text-slate-500 truncate"><?php echo e($item->user->unit_kerja ?? $item->pemohon_unit); ?></div>
                                </div>
                                <?php
                                    $badgeMap2 = ['diajukan' => 'badge-amber', 'diproses' => 'badge-blue', 'digunakan' => 'badge-cyan', 'selesai' => 'badge-emerald', 'tidak_disetujui' => 'badge-red'];
                                    $badgeClass2 = $badgeMap2[$item->status] ?? 'badge-slate';
                                    $label2 = match($item->status) {
                                        'diproses' => 'Disetujui',
                                        'digunakan' => 'Digunakan',
                                        'tidak_disetujui' => 'Tidak Disetujui',
                                        default => ucfirst($item->status)
                                    };
                                ?>
                                <span class="badge status-badge <?php echo e($badgeClass2); ?> whitespace-nowrap">
                                    <?php echo e($label2); ?>

                                </span>
                            </div>

                            <!-- Badges -->
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge status-badge <?php echo e($item->jenis === 'ambulance' ? 'bg-red-100 text-red-700' : 'badge-emerald'); ?>">
                                    <?php echo e(ucfirst($item->jenis)); ?>

                                </span>
                                <?php if($item->prioritas === 'segera'): ?>
                                    <span class="badge status-badge badge-red">CITO</span>
                                <?php endif; ?>
                            </div>

                            <!-- Details -->
                            <div class="mb-3 text-xs">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-slate-700 fw-500"><?php echo e($item->tanggal->format('d M Y')); ?></span>
                                    <span class="text-slate-500"><?php echo e($item->jam); ?> - <?php echo e($item->jam_sampai); ?></span>
                                </div>
                                <div class="text-xxs text-slate-400 mb-2">Dibuat: <?php echo e($item->created_at->format('d M Y, H:i')); ?></div>

                                <?php if($item->unit_mobil): ?>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span class="text-slate-700 text-capitalize"><?php echo e(str_replace('_', ' ', $item->unit_mobil)); ?></span>
                                        <?php if($item->plat_nomor): ?>
                                            <span class="text-slate-500 font-mono text-xxs">(<?php echo e($item->plat_nomor); ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if($item->alamat_tujuan): ?>
                                    <div class="d-flex align-items-start gap-2">
                                        <svg style="width:1rem;height:1rem;" class="text-slate-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-slate-600 flex-grow-1" style="overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;"><?php echo e($item->alamat_tujuan); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Button -->
                            <div class="d-flex flex-column gap-2">
                                <?php if(($item->status === 'diajukan' && !$item->signature_pemohon) || ($item->status === 'diproses' && !$item->signature_pengelola_1) || ($item->status === 'digunakan' && !$item->signature_driver)): ?>
                                    <div class="text-xxs text-amber-600 fw-600 text-center d-flex align-items-center justify-content-center gap-1">
                                        <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                        Tanda tangan belum lengkap
                                    </div>
                                <?php endif; ?>
                                <a href="<?php echo e(route('admin.transport.show', $item)); ?>" class="btn btn-sp-primary btn-sm w-100 text-center text-xs fw-600">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-5 text-center">
                            <svg style="width:3rem;height:3rem;" class="text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-slate-500 text-sm fw-500 mb-0">Belum ada data pengajuan transportasi.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <?php echo e($items->links()); ?>

            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $attributes = $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__attributesOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e)): ?>
<?php $component = $__componentOriginal7ae6b45c011e855a5545a671a7f3568e; ?>
<?php unset($__componentOriginal7ae6b45c011e855a5545a671a7f3568e); ?>
<?php endif; ?>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/transport/index.blade.php ENDPATH**/ ?>