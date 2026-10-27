<x-app-layout title="Tambah Jabatan — SIPETRANS">
    <div class="px-3 pt-3 pb-4" style="max-width:42rem;margin:0 auto;">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.job-roles.index') }}"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Tambah Jabatan Baru</h1>
        </div>

        <div class="sp-card p-4">
            <form action="{{ route('admin.job-roles.store') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Nama Jabatan *</label>
                    <input type="text" name="nama_role" value="{{ old('nama_role') }}" required
                           class="form-control form-control-sm @error('nama_role') is-invalid @enderror">
                    @error('nama_role')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                </div>
                <div class="mb-2">
                    <label class="form-label text-xxs fw-600 text-slate-600">Deskripsi</label>
                    <textarea name="deskripsi_role" rows="2" placeholder="Deskripsi jabatan (opsional)"
                              class="form-control form-control-sm">{{ old('deskripsi_role') }}</textarea>
                </div>
                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    @can('jabatan.create')
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Simpan</button>
                    @endcan
                    <a href="{{ route('admin.job-roles.index') }}"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
