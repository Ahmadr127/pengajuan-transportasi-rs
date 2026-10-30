<?php if (isset($component)) { $__componentOriginal7ae6b45c011e855a5545a671a7f3568e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7ae6b45c011e855a5545a671a7f3568e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'bb8545ae9a0d3757d1a0da063971df06::app','data' => ['title' => 'Tambah Driver — SIPETRANS']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tambah Driver — SIPETRANS']); ?>
    <div class="px-3 pt-3 pb-4" style="max-width:42rem;margin:0 auto;">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?php echo e(route('admin.drivers.index')); ?>"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Tambah Driver Baru</h1>
        </div>

        <div class="sp-card p-4">
            <form action="<?php echo e(route('admin.drivers.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Nama Lengkap *</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                           class="form-control form-control-sm <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback text-xxs"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nomor Telepon</label>
                        <input type="text" name="phone" value="<?php echo e(old('phone')); ?>"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nomor SIM</label>
                        <input type="text" name="license_number" value="<?php echo e(old('license_number')); ?>"
                               class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Akun Login Driver</label>
                    <?php if (isset($component)) { $__componentOriginal93f56a9791a1857c74a51e0e80d6e731 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-select','data' => ['name' => 'user_id','options' => collect($driverUsers)->map(fn($u) => ['value' => $u->id, 'label' => $u->full_name.' ('.$u->username.')'])->values()->all(),'selected' => old('user_id'),'placeholder' => '-- Tidak dihubungkan --']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($driverUsers)->map(fn($u) => ['value' => $u->id, 'label' => $u->full_name.' ('.$u->username.')'])->values()->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('user_id')),'placeholder' => '-- Tidak dihubungkan --']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $attributes = $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__attributesOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731)): ?>
<?php $component = $__componentOriginal93f56a9791a1857c74a51e0e80d6e731; ?>
<?php unset($__componentOriginal93f56a9791a1857c74a51e0e80d6e731); ?>
<?php endif; ?>
                    <p class="mt-1 text-xxs text-slate-400">Hubungkan ke akun dengan role "driver" agar driver bisa login</p>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Status</label>
                    <div class="d-flex align-items-center gap-4 mt-1">
                        <div class="form-check">
                            <input type="radio" name="is_active" value="1" <?php echo e(old('is_active', '1') === '1' ? 'checked' : ''); ?>

                                   class="form-check-input" id="is_active_1">
                            <label class="form-check-label text-xs text-slate-700" for="is_active_1">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="is_active" value="0" <?php echo e(old('is_active') === '0' ? 'checked' : ''); ?>

                                   class="form-check-input" id="is_active_0">
                            <label class="form-check-label text-xs text-slate-700" for="is_active_0">Nonaktif</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('driver.create')): ?>
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.drivers.index')); ?>"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
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
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/drivers/create.blade.php ENDPATH**/ ?>