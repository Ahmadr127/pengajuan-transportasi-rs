<x-app-layout title="Role & Permission — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Role & Permission</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola role dan hak akses menu/button secara dinamis</p>
            </div>
            @can('role.manage')
                <a href="{{ route('admin.roles.create') }}"
                   class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                    <span class="fw-600">Tambah Role</span>
                </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('error') }}
            </div>
        @endif

        <div class="sp-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase"
                            style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Role</th>
                            <th class="py-2 px-3">Kode</th>
                            <th class="py-2 px-3">Permission</th>
                            <th class="py-2 px-3">User</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr class="border-bottom border-slate-100">
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-800">{{ $role->label }}</div>
                                    <div class="text-xxs text-slate-500">{{ $role->description }}</div>
                                </td>
                                <td class="py-2 px-3">
                                    <code class="text-xxs">{{ $role->name }}</code>
                                    @if($role->is_super)
                                        <span class="badge badge-amber ms-1" style="font-size:.5625rem;">SUPER</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">{{ $role->permissions_count }}</td>
                                <td class="py-2 px-3">{{ $role->users_count }}</td>
                                <td class="py-2 px-3 text-end">
                                    @if($role->is_super)
                                        <span class="text-xxs text-slate-400">Terkunci</span>
                                    @else
                                        @can('role.manage')
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-sp-outline text-xxs">Edit</a>
                                            @if(! $role->is_locked && $role->users_count === 0)
                                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus role ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger text-xxs">Hapus</button>
                                                </form>
                                            @endif
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
