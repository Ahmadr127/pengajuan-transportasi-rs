@php
    $menu = $menu ?? null;
    $editing = $menu !== null;
    $action = $editing ? route('admin.menus.update', $menu) : route('admin.menus.store');
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Key *</label>
            <input type="text" name="key" value="{{ old('key', $menu->key ?? '') }}" required
                   class="form-control form-control-sm @error('key') is-invalid @enderror">
            @error('key')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Label *</label>
            <input type="text" name="label" value="{{ old('label', $menu->label ?? '') }}" required
                   class="form-control form-control-sm @error('label') is-invalid @enderror">
            @error('label')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Context *</label>
            <select name="context" class="form-select form-select-sm">
                @foreach($contexts as $context)
                    <option value="{{ $context }}" @selected(old('context', $menu->context ?? 'admin') === $context)>{{ ucfirst($context) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Section</label>
            <input type="text" name="section" value="{{ old('section', $menu->section ?? '') }}"
                   placeholder="mis. Master Data" class="form-control form-control-sm">
        </div>
        <div class="col-md-4">
            <label class="form-label text-xxs fw-600 text-slate-600">Urutan *</label>
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $menu->sort_order ?? 10) }}" required
                   class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Route Name</label>
            <input type="text" name="route_name" list="route-names" value="{{ old('route_name', $menu->route_name ?? '') }}"
                   class="form-control form-control-sm @error('route_name') is-invalid @enderror">
            <datalist id="route-names">
                @foreach($routeNames as $routeName)
                    <option value="{{ $routeName }}"></option>
                @endforeach
            </datalist>
            @error('route_name')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">URL (jika bukan route)</label>
            <input type="text" name="url" value="{{ old('url', $menu->url ?? '') }}" class="form-control form-control-sm">
        </div>

        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Permission</label>
            <x-searchable-select
                name="permission_name"
                :options="collect($permissions)->map(fn($p) => ['value' => $p, 'label' => $p])->values()->all()"
                :selected="old('permission_name', $menu->permission_name ?? '')"
                placeholder="— tanpa permission —" />
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Icon</label>
            <input type="text" name="icon" value="{{ old('icon', $menu->icon ?? '') }}" placeholder="dashboard, document, users..."
                   class="form-control form-control-sm">
        </div>

        <div class="col-12">
            <label class="form-label text-xxs fw-600 text-slate-600">Active Pattern</label>
            <input type="text" name="active_pattern" value="{{ old('active_pattern', $menu->active_pattern ?? '') }}"
                   placeholder="admin.transport.*,admin.transport.index" class="form-control form-control-sm">
        </div>

        <div class="col-12">
            <div class="d-flex flex-wrap gap-4 border-top border-slate-100 pt-3">
                @foreach([
                    'is_active' => 'Aktif',
                    'is_landing' => 'Landing page',
                    'show_sidebar' => 'Tampil di sidebar',
                    'show_bottom_nav' => 'Tampil di bottom nav',
                ] as $field => $label)
                    <div class="form-check">
                        <input type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}" class="form-check-input"
                               @checked(old($field, $menu->{$field} ?? ($field === 'is_active')))>
                        <label class="form-check-label text-xs text-slate-700" for="{{ $field }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 pt-3 mt-3 border-top border-slate-100">
        <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
        <a href="{{ route('admin.menus.index') }}" class="btn btn-sp-outline text-xs fw-600">Batal</a>
    </div>
</form>
