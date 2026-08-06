<x-app-layout title="Edit Driver — SIPETRANS">
    <div class="px-3 pt-3 pb-4" style="max-width:42rem;margin:0 auto;">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.drivers.index') }}"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Edit Driver — <span class="text-slate-500 fw-normal">{{ $driver->name }}</span></h1>
        </div>

        <div class="sp-card p-4">
            <form action="{{ route('admin.drivers.update', $driver) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $driver->name) }}" required
                           class="form-control form-control-sm @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nomor SIM</label>
                        <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}"
                               class="form-control form-control-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Akun Login Driver</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">-- Tidak dihubungkan --</option>
                        @foreach($driverUsers as $u)
                            <option value="{{ $u->id }}" {{ old('user_id', $driver->user_id) == $u->id ? 'selected' : '' }}>{{ $u->full_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xxs text-slate-400">Hubungkan ke akun dengan role "driver" agar driver bisa login</p>
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Status</label>
                    <div class="d-flex align-items-center gap-4 mt-1">
                        <div class="form-check">
                            <input type="radio" name="is_active" value="1"
                                   {{ old('is_active', $driver->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                                   class="form-check-input" id="is_active_1">
                            <label class="form-check-label text-xs text-slate-700" for="is_active_1">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="is_active" value="0"
                                   {{ old('is_active', $driver->is_active ? '1' : '0') === '0' ? 'checked' : '' }}
                                   class="form-check-input" id="is_active_0">
                            <label class="form-check-label text-xs text-slate-700" for="is_active_0">Nonaktif</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Update</button>
                    <a href="{{ route('admin.drivers.index') }}"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
