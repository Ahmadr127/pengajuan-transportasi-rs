<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Laporan — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Laporan — SIPETRANS']); ?>
    <div class="container-fluid px-3 px-sm-4 pt-4 pb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fs-5 fw-bold text-slate-900 mb-0">Laporan Pengajuan</h1>
                <p class="text-xs text-slate-500 mt-1 mb-0">Data lengkap seluruh pengajuan transportasi</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report.print')): ?>
                <a href="<?php echo e(route('admin.laporan.print', request()->query())); ?>"
                   target="_blank"
                   class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print PDF
                </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('report.export')): ?>
                <a href="<?php echo e(route('admin.laporan.export', request()->query())); ?>"
                   class="btn btn-sp-primary btn-sm d-inline-flex align-items-center gap-1">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export Excel
                </a>
                <?php endif; ?>
            </div>
        </div>

        
        <form method="GET" action="<?php echo e(route('admin.laporan')); ?>" id="laporan-filter-form">
        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100" style="min-width:1800px;">
                    <thead>
                        
                        <tr class="bg-slate-50 border-bottom border-slate-200">
                            <th class="px-2 py-2">
                                <input type="text" name="nomor" value="<?php echo e(request('nomor')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <div class="d-flex flex-column gap-1">
                                    <input type="date" name="tanggal_dari" value="<?php echo e(request('tanggal_dari')); ?>"
                                           class="form-control form-control-sm" style="font-size:.625rem;">
                                    <input type="date" name="tanggal_sampai_filter" value="<?php echo e(request('tanggal_sampai_filter')); ?>"
                                           class="form-control form-control-sm" style="font-size:.625rem;">
                                </div>
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="pemohon" value="<?php echo e(request('pemohon')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="unit_kerja" value="<?php echo e(request('unit_kerja')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <select name="jenis" class="form-select form-select-sm" style="font-size:.625rem;">
                                    <option value="">Semua</option>
                                    <option value="umum" <?php if(request('jenis') === 'umum'): echo 'selected'; endif; ?>>Umum</option>
                                    <option value="ambulance" <?php if(request('jenis') === 'ambulance'): echo 'selected'; endif; ?>>Ambulance</option>
                                </select>
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="keperluan" value="<?php echo e(request('keperluan')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <input type="text" name="tujuan" value="<?php echo e(request('tujuan')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="unit_mobil" value="<?php echo e(request('unit_mobil')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2">
                                <input type="text" name="plat_nomor" value="<?php echo e(request('plat_nomor')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <input type="text" name="supir" value="<?php echo e(request('supir')); ?>" placeholder="Cari..."
                                       class="form-control form-control-sm" style="font-size:.625rem;">
                            </th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2"></th>
                            <th class="px-2 py-2">
                                <div class="d-flex flex-column gap-1">
                                    <select name="status" class="form-select form-select-sm" style="font-size:.625rem;">
                                        <option value="">Semua</option>
                                        <option value="diajukan" <?php if(request('status') === 'diajukan'): echo 'selected'; endif; ?>>Diajukan</option>
                                        <option value="diproses" <?php if(request('status') === 'diproses'): echo 'selected'; endif; ?>>Disetujui</option>
                                        <option value="digunakan" <?php if(request('status') === 'digunakan'): echo 'selected'; endif; ?>>Digunakan</option>
                                        <option value="selesai" <?php if(request('status') === 'selesai'): echo 'selected'; endif; ?>>Selesai</option>
                                        <option value="tidak_disetujui" <?php if(request('status') === 'tidak_disetujui'): echo 'selected'; endif; ?>>Tdk Disetujui</option>
                                    </select>
                                    <select name="prioritas" class="form-select form-select-sm" style="font-size:.625rem;">
                                        <option value="">Semua Prioritas</option>
                                        <option value="biasa" <?php if(request('prioritas') === 'biasa'): echo 'selected'; endif; ?>>Biasa</option>
                                        <option value="segera" <?php if(request('prioritas') === 'segera'): echo 'selected'; endif; ?>>CITO</option>
                                    </select>
                                </div>
                            </th>
                        </tr>
                        
                        <tr>
                            <th class="whitespace-nowrap">No. Pengajuan</th>
                            <th class="whitespace-nowrap">Dibuat</th>
                            <th class="whitespace-nowrap">Nama Pemohon</th>
                            <th class="whitespace-nowrap">Unit Kerja</th>
                            <th class="whitespace-nowrap">Jenis</th>
                            <th class="whitespace-nowrap">Keperluan</th>
                            <th class="whitespace-nowrap">Tgl Berangkat</th>
                            <th class="whitespace-nowrap">Jam</th>
                            <th class="whitespace-nowrap">Tujuan</th>
                            <th class="whitespace-nowrap">Unit Kendaraan</th>
                            <th class="whitespace-nowrap">Plat Nomor</th>
                            <th class="whitespace-nowrap">NIP Driver</th>
                            <th class="whitespace-nowrap">Nama Driver</th>
                            <th class="whitespace-nowrap">KM Awal</th>
                            <th class="whitespace-nowrap">KM Akhir</th>
                            <th class="whitespace-nowrap">Jarak</th>
                            <th class="whitespace-nowrap">Tgl Kembali</th>
                            <th class="whitespace-nowrap">Jam Tiba</th>
                            <th class="whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $badgeMap = [
                                    'diajukan'       => 'badge-amber',
                                    'diproses'       => 'badge-blue',
                                    'digunakan'      => 'badge-cyan',
                                    'selesai'        => 'badge-emerald',
                                    'tidak_disetujui'=> 'badge-red',
                                ];
                                $statusLabel = match($item->status) {
                                    'diproses'       => 'Disetujui',
                                    'tidak_disetujui'=> 'Tdk Disetujui',
                                    default          => ucfirst($item->status),
                                };
                                $nipSupir = $item->driver && $item->driver->user ? $item->driver->user->nip : null;
                            ?>
                            <tr>
                                <td class="whitespace-nowrap">
                                    <span class="font-mono fw-600 text-slate-700"><?php echo e($item->nomor_pengajuan); ?></span>
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <div><?php echo e($item->created_at->format('d/m/Y')); ?></div>
                                    <div class="text-xxs text-slate-400"><?php echo e($item->created_at->format('H:i')); ?></div>
                                </td>
                                <td class="whitespace-nowrap">
                                    <div class="fw-500 text-slate-900"><?php echo e($item->user->full_name ?? $item->pemohon_nama); ?></div>
                                    <?php if($item->user && $item->user->isPriority()): ?>
                                        <span class="badge" style="font-size:.5625rem;background:#ede9fe;color:#6d28d9;">PRIORITAS</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-slate-600 whitespace-nowrap">
                                    <?php echo e($item->user->unit_kerja ?? $item->pemohon_unit ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="badge status-badge <?php echo e($item->jenis === 'ambulance' ? 'bg-red-100 text-red-700' : 'badge-emerald'); ?>">
                                        <?php echo e(ucfirst($item->jenis)); ?>

                                    </span>
                                    <?php if($item->prioritas === 'segera'): ?>
                                        <span class="badge status-badge badge-red ms-1">CITO</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-slate-600 truncate" style="max-width:7.5rem;" title="<?php echo e($item->keperluan); ?>">
                                    <?php echo e($item->keperluan ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-700">
                                    <?php echo e($item->tanggal->format('d/m/Y')); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <?php echo e(substr($item->jam, 0, 5)); ?>

                                    <?php if($item->jam_sampai): ?>
                                        <span class="text-slate-400">-</span> <?php echo e(substr($item->jam_sampai, 0, 5)); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="text-slate-600 truncate" style="max-width:8.75rem;" title="<?php echo e($item->alamat_tujuan); ?>">
                                    <?php echo e($item->alamat_tujuan ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-700">
                                    <?php if($item->unit_mobil): ?>
                                        <div class="fw-500 text-capitalize"><?php echo e(str_replace('_', ' ', $item->unit_mobil)); ?></div>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap font-mono text-slate-600">
                                    <?php echo e($item->plat_nomor ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap font-mono text-slate-600">
                                    <?php echo e($nipSupir ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <?php echo e($item->driver->name ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-600 text-end">
                                    <?php echo e($item->km_awal ? number_format($item->km_awal, 0, ',', '.') : '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-slate-600 text-end">
                                    <?php echo e($item->km_akhir ? number_format($item->km_akhir, 0, ',', '.') : '-'); ?>

                                </td>
                                <td class="whitespace-nowrap text-end">
                                    <?php if($item->km_awal && $item->km_akhir): ?>
                                        <span class="fw-600 text-emerald-700">+<?php echo e(number_format($item->km_akhir - $item->km_awal, 0, ',', '.')); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <?php if($item->tanggal_sampai): ?>
                                        <?php echo e($item->tanggal_sampai->format('d/m/Y')); ?>

                                    <?php elseif($item->status === 'selesai'): ?>
                                        <?php echo e($item->updated_at->format('d/m/Y')); ?>

                                    <?php else: ?>
                                        <span class="text-slate-400 text-xxs fst-italic">Sampai Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td class="whitespace-nowrap text-slate-600">
                                    <?php echo e($item->jam_kedatangan ?? '-'); ?>

                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="badge status-badge <?php echo e($badgeMap[$item->status] ?? 'badge-slate'); ?>">
                                        <?php echo e($statusLabel); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="19" class="px-3 py-5 text-center">
                                    <svg style="width:2.5rem;height:2.5rem;" class="text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-slate-500 text-xs fw-500 mb-0">Tidak ada data yang sesuai filter.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($items->hasPages()): ?>
                <div class="px-4 py-3 bg-slate-50 border-top border-slate-200">
                    <?php echo e($items->links()); ?>

                </div>
            <?php endif; ?>
        </div>
        </form>

        <div class="mt-2 text-xxs text-slate-400">
            Menampilkan <?php echo e($items->firstItem() ?? 0); ?>&ndash;<?php echo e($items->lastItem() ?? 0); ?> dari <?php echo e($items->total()); ?> data
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('laporan-filter-form');
            if (!form) return;

            let debounceTimer;

            function submitClean(delay) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    form.querySelectorAll('input, select').forEach(el => {
                        if (el.value === '') el.disabled = true;
                    });
                    form.submit();
                }, delay ?? 500);
            }

            form.querySelectorAll('input[type="text"], input[type="date"]').forEach(function (input) {
                input.addEventListener('input', () => submitClean(500));
            });

            form.querySelectorAll('select').forEach(function (select) {
                select.addEventListener('change', () => submitClean(0));
            });
        });
    </script>
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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/laporan.blade.php ENDPATH**/ ?>