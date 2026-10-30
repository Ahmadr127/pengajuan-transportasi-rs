<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['items']));

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

foreach (array_filter((['items']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<nav class="px-3 py-3">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($section): ?>
            <div class="sp-sidebar-section"><?php echo e($section); ?></div>
        <?php endif; ?>

        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($menu->href()); ?>" @click="sidebarOpen = false"
               class="sp-sidebar-link <?php echo e($menu->isActive() ? 'active' : ''); ?>">
                <span class="sp-sidebar-icon">
                    <?php if (isset($component)) { $__componentOriginal32e25d16903f301ccd3ed110350a3581 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32e25d16903f301ccd3ed110350a3581 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.menu-icon','data' => ['name' => $menu->icon]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($menu->icon)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32e25d16903f301ccd3ed110350a3581)): ?>
<?php $attributes = $__attributesOriginal32e25d16903f301ccd3ed110350a3581; ?>
<?php unset($__attributesOriginal32e25d16903f301ccd3ed110350a3581); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32e25d16903f301ccd3ed110350a3581)): ?>
<?php $component = $__componentOriginal32e25d16903f301ccd3ed110350a3581; ?>
<?php unset($__componentOriginal32e25d16903f301ccd3ed110350a3581); ?>
<?php endif; ?>
                </span>
                <span><?php echo e($menu->label); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/components/sidebar.blade.php ENDPATH**/ ?>