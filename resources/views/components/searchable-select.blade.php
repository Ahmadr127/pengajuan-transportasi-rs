@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => '-- Pilih --',
    'required' => false,
])

<div x-data="{
    open: false,
    search: '',
    value: @js((string) $selected),
    label: '',
    options: @js($options),
    get filtered() {
        if (!this.search) return this.options;
        const q = this.search.toLowerCase();
        return this.options.filter(o => String(o.label ?? '').toLowerCase().includes(q));
    },
    init() {
        const found = this.options.find(o => String(o.value) === String(this.value));
        this.label = found ? found.label : '';
    },
    select(o) { this.value = o.value; this.label = o.label; this.open = false; this.search = ''; }
}" class="position-relative" @click.outside="open = false">
    <input type="hidden" name="{{ $name }}" :value="value" @if($required) required @endif>
    <button type="button"
            class="form-select form-select-sm text-start d-flex align-items-center justify-content-between"
            @click="open = !open">
        <span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="label || '{{ $placeholder }}'"
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
