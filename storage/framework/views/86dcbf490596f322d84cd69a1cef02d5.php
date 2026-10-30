<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Master Jabatan — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Master Jabatan — SIPETRANS']); ?>
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Master Jabatan</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola data jabatan pegawai (dari Simutu)</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('jabatan.create')): ?>
            <a href="<?php echo e(route('admin.job-roles.create')); ?>"
               class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="fw-600">Tambah</span>
            </a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <form method="GET" action="<?php echo e(route('admin.job-roles.index')); ?>" id="job-roles-filter-form">
            <div class="sp-card overflow-hidden mb-3">
                <div class="overflow-x-auto">
                    <table class="sp-table w-100 text-xs">
                        <thead>
                            
                            <tr class="bg-slate-50 border-bottom border-slate-200">
                                <th class="py-2 px-3">
                                    <input type="text" name="nama" value="<?php echo e(request('nama')); ?>" placeholder="Cari jabatan..."
                                           class="form-control form-control-sm text-xxs border-slate-300">
                                </th>
                                <th class="py-2 px-3"></th>
                                <th class="py-2 px-3"></th>
                            </tr>
                            
                            <tr class="text-start text-xxs fw-600 text-white text-uppercase" style="background: linear-gradient(to right, #007774, #009e9a);">
                                <th class="py-2 px-3">Nama Jabatan</th>
                                <th class="py-2 px-3">Deskripsi</th>
                                <th class="py-2 px-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php $__empty_1 = true; $__currentLoopData = $jobRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jobRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="py-2 px-3"><div class="fw-600 text-slate-900"><?php echo e($jobRole->nama_role); ?></div></td>
                                    <td class="py-2 px-3 text-slate-600"><?php echo e($jobRole->deskripsi_role ?? '-'); ?></td>
                                    <td class="py-2 px-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('jabatan.edit')): ?>
                                            <a href="<?php echo e(route('admin.job-roles.edit', $jobRole)); ?>" class="btn-action-edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('jabatan.delete')): ?>
                                            <button type="button" class="btn-action-delete"
                                                    onclick="deleteJobRole('<?php echo e(route('admin.job-roles.destroy', $jobRole)); ?>')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="py-5 text-center text-slate-500 text-xs">Tidak ada data jabatan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
                    <?php echo e($jobRoles->links()); ?>

                </div>
            </div>
        </form>

        <form id="delete-job-role-form" method="POST" style="display:none;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>

        <script>
            function deleteJobRole(url) {
                if (!confirm('Yakin ingin menghapus jabatan ini?')) return;
                const form = document.getElementById('delete-job-role-form');
                form.action = url;
                form.submit();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('job-roles-filter-form');
                if (!form) return;
                let timer;
                form.querySelectorAll('input[type="text"]').forEach(el => {
                    el.addEventListener('input', () => {
                        clearTimeout(timer);
                        timer = setTimeout(() => form.submit(), 400);
                    });
                });
            });
        </script>

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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/job-roles/index.blade.php ENDPATH**/ ?>