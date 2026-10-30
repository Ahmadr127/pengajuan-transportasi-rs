<?php
    $menu = $menu ?? null;
    $editing = $menu !== null;
    $action = $editing ? route('admin.menus.update', $menu) : route('admin.menus.store');
?>

<form action="<?php echo e($action); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Key *</label>
            <input type="text" name="key" value="<?php echo e(old('key', $menu->key ?? '')); ?>" required
                   class="form-control form-control-sm <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['key'];
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
            <input type="text" name="label" value="<?php echo e(old('label', $menu->label ?? '')); ?>" required
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

        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Context *</label>
            <select name="context" class="form-select form-select-sm">
                <?php $__currentLoopData = $contexts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $context): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($context); ?>" <?php if(old('context', $menu->context ?? 'admin') === $context): echo 'selected'; endif; ?>><?php echo e(ucfirst($context)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Menu Induk</label>
            <select name="parent_id" class="form-select form-select-sm">
                <option value="">— tanpa induk —</option>
                <?php $__currentLoopData = collect($parents ?? [])->groupBy('context'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parentContext => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <optgroup label="<?php echo e(ucfirst($parentContext)); ?>">
                        <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($parent->id); ?>" <?php if((string) old('parent_id', $menu->parent_id ?? '') === (string) $parent->id): echo 'selected'; endif; ?>>
                                <?php echo e($parent->label); ?> (<?php echo e($parent->key); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="form-text text-xxs">Dipakai sebagai sub-menu di mobile (mis. Master).</div>
        </div>
        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Section</label>
            <input type="text" name="section" value="<?php echo e(old('section', $menu->section ?? '')); ?>"
                   placeholder="mis. Master Data" class="form-control form-control-sm">
        </div>
        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Urutan *</label>
            <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $menu->sort_order ?? 10)); ?>" required
                   class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Route Name</label>
            <input type="text" name="route_name" list="route-names" value="<?php echo e(old('route_name', $menu->route_name ?? '')); ?>"
                   class="form-control form-control-sm <?php $__errorArgs = ['route_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <datalist id="route-names">
                <?php $__currentLoopData = $routeNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routeName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($routeName); ?>"></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </datalist>
            <?php $__errorArgs = ['route_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback text-xxs"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">URL (jika bukan route)</label>
            <input type="text" name="url" value="<?php echo e(old('url', $menu->url ?? '')); ?>" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Permission</label>
            <?php if (isset($component)) { $__componentOriginal93f56a9791a1857c74a51e0e80d6e731 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal93f56a9791a1857c74a51e0e80d6e731 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.searchable-select','data' => ['name' => 'permission_name','options' => collect($permissions)->map(fn($p) => ['value' => $p, 'label' => $p])->values()->all(),'selected' => old('permission_name', $menu->permission_name ?? ''),'placeholder' => '— tanpa permission —']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('searchable-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'permission_name','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($permissions)->map(fn($p) => ['value' => $p, 'label' => $p])->values()->all()),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('permission_name', $menu->permission_name ?? '')),'placeholder' => '— tanpa permission —']); ?>
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
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Icon</label>
            <input type="text" name="icon" value="<?php echo e(old('icon', $menu->icon ?? '')); ?>" placeholder="dashboard, document, users..."
                   class="form-control form-control-sm">
        </div>

        <div class="col-12">
            <label class="form-label text-xxs fw-600 text-slate-600">Active Pattern</label>
            <input type="text" name="active_pattern" value="<?php echo e(old('active_pattern', $menu->active_pattern ?? '')); ?>"
                   placeholder="admin.transport.*,admin.transport.index" class="form-control form-control-sm">
        </div>

        <div class="col-12">
            <div class="d-flex flex-wrap gap-4 border-top border-slate-100 pt-3">
                <?php $__currentLoopData = [
                    'is_active' => 'Aktif',
                    'is_landing' => 'Landing page',
                    'show_sidebar' => 'Tampil di sidebar',
                    'show_bottom_nav' => 'Tampil di bottom nav',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-check">
                        <input type="checkbox" name="<?php echo e($field); ?>" value="1" id="<?php echo e($field); ?>" class="form-check-input"
                               <?php if(old($field, $menu->{$field} ?? ($field === 'is_active'))): echo 'checked'; endif; ?>>
                        <label class="form-check-label text-xs text-slate-700" for="<?php echo e($field); ?>"><?php echo e($label); ?></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 pt-3 mt-3 border-top border-slate-100">
        <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
        <a href="<?php echo e(route('admin.menus.index')); ?>" class="btn btn-sp-outline text-xs fw-600">Batal</a>
    </div>
</form>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/admin/menus/_form.blade.php ENDPATH**/ ?>