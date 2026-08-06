<x-app-layout title="Edit Kendaraan — SIPETRANS">
    <div class="px-3 pt-3 pb-4" style="max-width:42rem;margin:0 auto;"
         x-data="{ editType: '{{ old('type', $vehicle->type) }}' }">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.vehicles.index') }}"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Edit Kendaraan — <span class="text-slate-500 fw-normal">{{ $vehicle->name }}</span></h1>
        </div>

        <div class="sp-card p-4">
            <form action="{{ route('admin.vehicles.update', $vehicle) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nama Unit *</label>
                        <input type="text" name="name" value="{{ old('name', $vehicle->name) }}" required
                               class="form-control form-control-sm @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Jenis *</label>
                        <select name="type" x-model="editType" required
                                class="form-select form-select-sm">
                            <option value="umum">Umum</option>
                            <option value="ambulance">Ambulance</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nomor Polisi *</label>
                        <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required
                               class="form-control form-control-sm @error('plate_number') is-invalid @enderror">
                        @error('plate_number')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Merk</label>
                        <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}"
                               class="form-control form-control-sm">
                    </div>
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Model/Tipe</label>
                        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Status</label>
                        <div class="d-flex align-items-center gap-4 mt-1">
                            <div class="form-check">
                                <input type="radio" name="is_active" value="1"
                                       {{ old('is_active', $vehicle->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                                       class="form-check-input" id="is_active_1">
                                <label class="form-check-label text-xs text-slate-700" for="is_active_1">Aktif</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="is_active" value="0"
                                       {{ old('is_active', $vehicle->is_active ? '1' : '0') === '0' ? 'checked' : '' }}
                                       class="form-check-input" id="is_active_0">
                                <label class="form-check-label text-xs text-slate-700" for="is_active_0">Nonaktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Catatan</label>
                    <textarea name="notes" rows="2"
                              class="form-control form-control-sm">{{ old('notes', $vehicle->notes) }}</textarea>
                </div>
                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Update</button>
                    <a href="{{ route('admin.vehicles.index') }}"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
