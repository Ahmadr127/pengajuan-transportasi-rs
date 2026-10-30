<x-app title="Edit Unit — SIPETRANS">
    <div class="px-3 pt-3 pb-4" style="max-width:42rem;margin:0 auto;">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.units.index') }}"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Edit Unit — <span class="text-slate-500 fw-normal">{{ $unit->nama_unit }}</span></h1>
        </div>

        <div class="sp-card p-4">
            <form action="{{ route('admin.units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Kode Unit *</label>
                        <input type="text" name="kode_unit" value="{{ old('kode_unit', $unit->kode_unit) }}" required
                               class="form-control form-control-sm @error('kode_unit') is-invalid @enderror">
                        @error('kode_unit')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nama Unit *</label>
                        <input type="text" name="nama_unit" value="{{ old('nama_unit', $unit->nama_unit) }}" required
                               class="form-control form-control-sm @error('nama_unit') is-invalid @enderror">
                        @error('nama_unit')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Status</label>
                        <select name="status_unit" class="form-select form-select-sm">
                            <option value="aktif" @selected(old('status_unit', $unit->status_unit) === 'aktif')>Aktif</option>
                            <option value="non-aktif" @selected(old('status_unit', $unit->status_unit) === 'non-aktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Deskripsi</label>
                    <textarea name="deskripsi_unit" rows="2"
                              class="form-control form-control-sm">{{ old('deskripsi_unit', $unit->deskripsi_unit) }}</textarea>
                </div>
                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    @can('unit.edit')
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Update</button>
                    @endcan
                    <a href="{{ route('admin.units.index') }}"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app>
