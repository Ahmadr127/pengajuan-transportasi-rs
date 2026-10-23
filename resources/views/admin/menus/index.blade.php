<x-app-layout title="Menu — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Menu Navigasi</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Menu sidebar, bottom nav, dan halaman awal ditentukan dari sini</p>
            </div>
            <a href="{{ route('admin.menus.create') }}" class="btn btn-sp-primary text-xs fw-600">Tambah Menu</a>
        </div>

        @if(session('success'))
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">{{ session('success') }}</div>
        @endif

        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase"
                            style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Menu</th>
                            <th class="py-2 px-3">Context</th>
                            <th class="py-2 px-3">Route</th>
                            <th class="py-2 px-3">Permission</th>
                            <th class="py-2 px-3">Urutan</th>
                            <th class="py-2 px-3">Tampil</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menus as $menu)
                            <tr class="border-bottom border-slate-100">
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-800">
                                        {{ $menu->label }}
                                        @if($menu->is_landing)
                                            <span class="badge badge-blue ms-1" style="font-size:.5625rem;">LANDING</span>
                                        @endif
                                    </div>
                                    <div class="text-xxs text-slate-500">
                                        <code>{{ $menu->key }}</code>
                                        @if($menu->section) · {{ $menu->section }} @endif
                                    </div>
                                </td>
                                <td class="py-2 px-3">{{ $menu->context }}</td>
                                <td class="py-2 px-3"><code class="text-xxs">{{ $menu->route_name ?: $menu->url }}</code></td>
                                <td class="py-2 px-3"><code class="text-xxs">{{ $menu->permission_name ?: '—' }}</code></td>
                                <td class="py-2 px-3">{{ $menu->sort_order }}</td>
                                <td class="py-2 px-3 text-xxs">
                                    {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    @if($menu->show_sidebar) · Sidebar @endif
                                    @if($menu->show_bottom_nav) · Bottom @endif
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <a href="{{ route('admin.menus.edit', $menu) }}" class="btn btn-sm btn-sp-outline text-xxs">Edit</a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger text-xxs">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
