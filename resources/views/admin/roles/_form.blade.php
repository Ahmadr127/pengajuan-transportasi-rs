@php
    $role = $role ?? null;
    $rolePermissions = $rolePermissions ?? [];
    $editing = $role !== null;
    $action = $editing ? route('admin.roles.update', $role) : route('admin.roles.store');
    $selected = old('permissions', $rolePermissions);
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Kode Role *</label>
            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" required
                   class="form-control form-control-sm @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label text-xxs fw-600 text-slate-600">Label *</label>
            <input type="text" name="label" value="{{ old('label', $role->label ?? '') }}" required
                   class="form-control form-control-sm @error('label') is-invalid @enderror">
            @error('label')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label text-xxs fw-600 text-slate-600">Deskripsi</label>
            <input type="text" name="description" value="{{ old('description', $role->description ?? '') }}"
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
            @foreach($permissions as $group => $items)
                <div class="col-md-6">
                    <div class="sp-card p-3 h-100">
                        <div class="text-xxs fw-bold text-uppercase mb-2" style="color:#007774;">{{ $group }}</div>
                        @foreach($items as $permission)
                            <div class="form-check mb-1">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       id="perm_{{ $permission->id }}" class="form-check-input perm-check"
                                       @checked(in_array($permission->id, (array) $selected))>
                                <label for="perm_{{ $permission->id }}" class="form-check-label text-xs text-slate-700">
                                    {{ $permission->label }}
                                    <code class="text-xxs text-slate-400">{{ $permission->name }}</code>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 pt-3 mt-3 border-top border-slate-100">
        <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sp-outline text-xs fw-600">Batal</a>
    </div>
</form>
