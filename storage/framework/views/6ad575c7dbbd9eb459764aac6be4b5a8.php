<?php
    $role = $role ?? null;
    $rolePermissions = $rolePermissions ?? [];
    $editing = $role !== null;
    $action = $editing ? route('admin.roles.update', $role) : route('admin.roles.store');
    $selected = old('permissions', $rolePermissions);
?>

<form action="<?php echo e($action); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Kode Role *</label>
            <input type="text" name="name" value="<?php echo e(old('name', $role->name ?? '')); ?>" required
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
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Label *</label>
            <input type="text" name="label" value="<?php echo e(old('label', $role->label ?? '')); ?>" required
                   class="form-control form-control-sm <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback text-xxs"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-12">
            <label class="form-label text-xxs fw-600 text-slate-600">Deskripsi</label>
            <input type="text" name="description" value="<?php echo e(old('description', $role->description ?? '')); ?>"
                   class="form-control form-control-sm">
        </div>
    </div>

    <div class="border-top border-slate-100 pt-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-600 text-xs text-slate-700">Permission</div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-sp-outline text-xxs" onclick="document.querySelectorAll('.perm-check').forEach(c => c.checked = true)">Pilih semua</button>
                <button type="button" class="btn btn-sm btn-sp-outline text-xxs" onclick="document.querySelectorAll('.perm-check').forEach(c => c.checked = false)">Kosongkan</button>
            </div>
        </div>

        <div class="row g-3">
            <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <div class="sp-card p-3 h-100">
                        <div class="text-xxs fw-bold text-uppercase mb-2" style="color:#007774;"><?php echo e($group); ?></div>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check mb-1">
                                <input type="checkbox" name="permissions[]" value="<?php echo e($permission->id); ?>"
                                       id="perm_<?php echo e($permission->id); ?>" class="form-check-input perm-check"
                                       <?php if(in_array($permission->id, (array) $selected)): echo 'checked'; endif; ?>>
                                <label for="perm_<?php echo e($permission->id); ?>" class="form-check-label text-xs text-slate-700">
                                    <?php echo e($permission->label); ?>

                                    <code class="text-xxs text-slate-400"><?php echo e($permission->name); ?></code>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 pt-3 mt-3 border-top border-slate-100">
        <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
        <a href="<?php echo e(route('admin.roles.index')); ?>" class="btn btn-sp-outline text-xs fw-600">Batal</a>
    </div>
</form>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/roles/_form.blade.php ENDPATH**/ ?>