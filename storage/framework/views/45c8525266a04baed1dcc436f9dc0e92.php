<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => '-- Pilih --',
    'required' => false,
    'id' => null,
]));

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

foreach (array_filter(([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => '-- Pilih --',
    'required' => false,
    'id' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="{
    open: false,
    search: '',
    value: <?php echo \Illuminate\Support\Js::from((string) $selected)->toHtml() ?>,
    label: '',
    options: <?php echo \Illuminate\Support\Js::from($options)->toHtml() ?>,
    get filtered() {
        if (!this.search) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => String(o.label ?? '').toLowerCase().includes(q));
    },
    init() {
        const found = this.options.find(o => String(o.value) === String(this.value));
        this.label = found ? found.label : '';
        this.$nextTick(() => {
            const el = this.$refs.hidden;
            if (el && this.value !== '') {
                el.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: { value: this.value, option: found } }));
            }
        });
    },
    select(o) {
        this.value = o.value;
        this.label = o.label;
        this.open = false;
        this.search = '';
        this.$nextTick(() => {
            const el = this.$refs.hidden;
            if (el) {
                el.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: { value: o.value, option: o } }));
            }
        });
    }
}" class="position-relative" @click.outside="open = false">
    <input type="hidden" name="<?php echo e($name); ?>" id="<?php echo e($id ?? $name); ?>" :value="value" <?php if($required): ?> required <?php endif; ?>
           x-ref="hidden"
           @change.stop>
    <button type="button"
            class="form-select form-select-sm text-start d-flex align-items-center justify-content-between"
            @click="open = !open">
        <span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="label || '<?php echo e($placeholder); ?>'"
              :class="label ? '' : 'text-slate-400'"></span>
        <svg style="width:.75rem;height:.75rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div x-show="open" x-transition
         class="position-absolute w-100 bg-white border border-slate-200 rounded shadow-sm mt-1"
         style="z-index:1050; max-height:260px; overflow:auto;">
        <div class="p-2 border-bottom border-slate-100">
            <input type="text" x-model="search" placeholder="Cari..."
                   class="form-control form-control-sm" @click.stop>
        </div>
        <div class="py-1">
            <template x-for="o in filtered" :key="o.value">
                <div class="px-3 py-1 cursor-pointer text-xs"
                     :class="String(o.value) === String(value) ? 'bg-slate-100 fw-600' : 'hover:bg-slate-50'"
                     @click="select(o)" x-text="o.label"></div>
            </template>
            <div x-show="filtered.length === 0" class="px-3 py-2 text-xxs text-slate-400">Tidak ada hasil</div>
        </div>
    </div>
</div>
<?php /**PATH E:\kerjaan\Programming\pengajuan-transportasi-rs\resources\views/components/searchable-select.blade.php ENDPATH**/ ?>