<x-app title="Tambah Role — SIPETRANS">
    <div class="px-3 pt-3 pb-4"> {{-- jnagan ubah lebar lagi --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">Kembali</a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Tambah Role Baru</h1>
        </div>

        <div class="sp-card p-4">
            @include('admin.roles._form', ['role' => null, 'rolePermissions' => []])
        </div>
    </div>
</x-app>
