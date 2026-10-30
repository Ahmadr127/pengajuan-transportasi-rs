<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Role & Permission — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Role & Permission — SIPETRANS']); ?>
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Role & Permission</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola role dan hak akses menu/button secara dinamis</p>
            </div>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role.manage')): ?>
                <a href="<?php echo e(route('admin.roles.create')); ?>"
                   class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                    <span class="fw-600">Tambah Role</span>
                </a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger rounded px-3 py-2 mb-3 text-xs fw-500">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase"
                            style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Role</th>
                            <th class="py-2 px-3">Kode</th>
                            <th class="py-2 px-3">Permission</th>
                            <th class="py-2 px-3">User</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-bottom border-slate-100">
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-800"><?php echo e($role->label); ?></div>
                                    <div class="text-xxs text-slate-500"><?php echo e($role->description); ?></div>
                                </td>
                                <td class="py-2 px-3">
                                    <code class="text-xxs"><?php echo e($role->name); ?></code>
                                    <?php if($role->is_super): ?>
                                        <span class="badge badge-amber ms-1" style="font-size:.5625rem;">SUPER</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3"><?php echo e($role->permissions_count); ?></td>
                                <td class="py-2 px-3"><?php echo e($role->users_count); ?></td>
                                <td class="py-2 px-3 text-end">
                                    <?php if($role->is_super): ?>
                                        <span class="text-xxs text-slate-400">Terkunci</span>
                                    <?php else: ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role.manage')): ?>
                                            <a href="<?php echo e(route('admin.roles.edit', $role)); ?>" class="btn btn-sm btn-sp-outline text-xxs">Edit</a>
                                            <?php if(! $role->is_locked && $role->users_count === 0): ?>
                                                <form action="<?php echo e(route('admin.roles.destroy', $role)); ?>" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus role ini?')">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger text-xxs">Hapus</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>