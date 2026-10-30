<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Master Pengajuan Berulang — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Master Pengajuan Berulang — SIPETRANS']); ?>
    <div class="container-fluid px-3 pt-4 pb-4">
        <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">
            Master Pengajuan Berulang
        </h1>
        <p class="text-slate-500 mt-1 mb-0 text-xs">
            Kelola template jadwal pengajuan transportasi otomatis
        </p>

        <?php if(session('success')): ?>
            <div class="alert-sp-success mt-3 text-xs fw-500">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="mt-3">
            <!-- Filters -->
            <form method="GET" action="<?php echo e(route('admin.recurring-templates.index')); ?>" class="sp-card p-2 mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <select name="jenis" class="form-select form-select-sm text-xs border-slate-300" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="umum" <?php if(request('jenis') === 'umum'): echo 'selected'; endif; ?>>Mobil Umum</option>
                        <option value="ambulance" <?php if(request('jenis') === 'ambulance'): echo 'selected'; endif; ?>>Ambulance</option>
                    </select>

                    <select name="status" class="form-select form-select-sm text-xs border-slate-300" style="width:auto;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif" <?php if(request('status') === 'aktif'): echo 'selected'; endif; ?>>Aktif</option>
                        <option value="nonaktif" <?php if(request('status') === 'nonaktif'): echo 'selected'; endif; ?>>Nonaktif</option>
                    </select>

                    <?php if(request()->hasAny(['jenis', 'status'])): ?>
                        <a href="<?php echo e(route('admin.recurring-templates.index')); ?>" class="btn btn-sm btn-sp-outline text-xs fw-500">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="sp-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="sp-table w-100">
                        <thead>
                            <tr class="text-start text-xxs fw-600 text-white text-uppercase" style="background: linear-gradient(to right, #007774, #009e9a);">
                                <th class="px-3 py-3">Pemohon</th>
                                <th class="px-3 py-3">Jenis/Keperluan</th>
                                <th class="px-3 py-3">Jam Pengajuan</th>
                                <th class="px-3 py-3">Periode</th>
                                <th class="px-3 py-3">Hari Aktif</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-3 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-3 py-2">
                                        <div class="fw-600 text-xs text-slate-900"><?php echo e($item->pemohon_nama); ?></div>
                                        <div class="text-xxs text-slate-500"><?php echo e($item->pemohon_unit); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <?php if($item->jenis === 'ambulance'): ?>
                                                <span class="badge-red"><?php echo e(ucfirst($item->jenis)); ?></span>
                                            <?php else: ?>
                                                <span class="badge-emerald"><?php echo e(ucfirst($item->jenis)); ?></span>
                                            <?php endif; ?>
                                            <?php if($item->prioritas === 'segera'): ?>
                                                <span class="badge-red text-xxs fw-bold">CITO</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xxs fw-500"><?php echo e($item->keperluan); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xs text-slate-700 fw-500 text-nowrap"><?php echo e(substr($item->jam, 0, 5)); ?> <?php echo e($item->jam_sampai ? '- '.substr($item->jam_sampai,0,5) : '- Selesai'); ?></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-xxs text-slate-700 fw-500 text-nowrap">Sejak: <?php echo e($item->start_date->format('d/m/Y')); ?></div>
                                        <div class="text-xxs text-slate-500 fw-500 text-nowrap">Terus Menerus</div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <?php
                                            $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                                            $activeDays = array_map(fn($d) => $dayNames[$d] ?? '', $item->hari ?? []);
                                        ?>
                                        <div class="d-flex flex-wrap gap-1" style="max-width:150px;">
                                            <?php $__currentLoopData = $activeDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="badge-slate text-xxs fw-500">
                                                    <?php echo e($day); ?>

                                                </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <?php if($item->is_active): ?>
                                            <span class="badge-emerald">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge-slate">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('recurring.edit')): ?>
                                            <a href="<?php echo e(route('admin.recurring-templates.edit', $item)); ?>"
                                               class="btn-action-edit" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('recurring.delete')): ?>
                                            <form action="<?php echo e(route('admin.recurring-templates.destroy', $item)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini? Pengajuan yang sudah terbuat tidak akan terhapus.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                        class="btn-action-delete" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-3 py-5 text-center">
                                        <svg class="text-slate-300 mx-auto mb-2" style="width:40px;height:40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        <p class="text-slate-500 text-xs fw-500 mb-0">Belum ada data template pengajuan berulang.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/recurring-templates/index.blade.php ENDPATH**/ ?>