<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Menu — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Menu — SIPETRANS']); ?>
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Menu Navigasi</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Menu sidebar, bottom nav, dan halaman awal ditentukan dari sini</p>
            </div>
            <a href="<?php echo e(route('admin.menus.create')); ?>" class="btn btn-sp-primary text-xs fw-600">Tambah Menu</a>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase"
                            style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Menu</th>
                            <th class="py-2 px-3">Context</th>
                            <th class="py-2 px-3">Route</th>
                            <th class="py-2 px-3">Permission</th>
                            <th class="py-2 px-3">Urutan</th>
                            <th class="py-2 px-3">Tampil</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-bottom border-slate-100">
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-800">
                                        <?php echo e($menu->label); ?>

                                        <?php if($menu->is_landing): ?>
                                            <span class="badge badge-blue ms-1" style="font-size:.5625rem;">LANDING</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xxs text-slate-500">
                                        <code><?php echo e($menu->key); ?></code>
                                        <?php if($menu->section): ?> · <?php echo e($menu->section); ?> <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-2 px-3"><?php echo e($menu->context); ?></td>
                                <td class="py-2 px-3"><code class="text-xxs"><?php echo e($menu->route_name ?: $menu->url); ?></code></td>
                                <td class="py-2 px-3"><code class="text-xxs"><?php echo e($menu->permission_name ?: '—'); ?></code></td>
                                <td class="py-2 px-3"><?php echo e($menu->sort_order); ?></td>
                                <td class="py-2 px-3 text-xxs">
                                    <?php echo e($menu->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                    <?php if($menu->show_sidebar): ?> · Sidebar <?php endif; ?>
                                    <?php if($menu->show_bottom_nav): ?> · Bottom <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <a href="<?php echo e(route('admin.menus.edit', $menu)); ?>" class="btn btn-sm btn-sp-outline text-xxs">Edit</a>
                                    <form action="<?php echo e(route('admin.menus.destroy', $menu)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus menu ini?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger text-xxs">Hapus</button>
                                    </form>
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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/menus/index.blade.php ENDPATH**/ ?>