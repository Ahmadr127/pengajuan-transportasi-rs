<x-app title="Edit Menu — SIPETRANS">
    <div class="px-3 pt-3 pb-4"> {{-- jnagan ubah lebar lagi --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.menus.index') }}" class="btn btn-sp-outline text-xs fw-500">Kembali</a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Edit Menu: {{ $menu->label }}</h1>
        </div>
        <div class="sp-card p-4">
            @include('admin.menus._form', ['menu' => $menu])
        </div>
    </div>
</x-app>
