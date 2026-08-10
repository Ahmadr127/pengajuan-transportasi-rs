<x-app-layout title="Manajemen Pengguna — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Master User</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola data user sistem</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form method="POST" action="{{ route('admin.users.sync-simutu') }}" onsubmit="return confirm('Sinkronkan data pegawai dari Simutu? User yang belum ada akan dibuat otomatis.');">
                    @csrf
                    <button type="submit"
                            class="btn btn-outline-sp d-inline-flex align-items-center gap-2 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M3.35 9a9 9 0 0114.3-3.36L21 9M20.65 15a9 9 0 01-14.3 3.36L3 15"/>
                        </svg>
                        <span class="fw-600">Sync Simutu</span>
                    </button>
                </form>
                <a href="{{ route('admin.users.create') }}"
                   class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="fw-600">Tambah</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-sp-danger border border-red-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('error') }}
            </div>
        @endif

        {{-- Hidden form untuk delete (di luar tabel agar tidak nested) --}}
        <form id="delete-user-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- Desktop Table -->
        <div class="d-none d-md-block sp-card overflow-hidden mb-3">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        {{-- Baris 1: Filter --}}
                        <form method="GET" action="{{ route('admin.users.index') }}" id="users-filter-form">
                        <tr class="bg-slate-50 border-bottom border-slate-200">
                            <th class="py-2 px-3">
                                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="username" value="{{ request('username') }}" placeholder="Cari username..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="nip" value="{{ request('nip') }}" placeholder="Cari NIP..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="unit_kerja" value="{{ request('unit_kerja') }}" placeholder="Cari unit..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="posisi" value="{{ request('posisi') }}" placeholder="Cari posisi..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="profesi" value="{{ request('profesi') }}" placeholder="Cari profesi..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="jabatan" value="{{ request('jabatan') }}" placeholder="Cari jabatan..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <select name="role" class="form-select form-select-sm text-xxs border-slate-300">
                                    <option value="">Semua</option>
                                    <option value="user" @selected(request('role') === 'user')>User</option>
                                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                                    <option value="driver" @selected(request('role') === 'driver')>Driver</option>
                                </select>
                            </th>
                            <th class="py-2 px-3"></th>
                        </tr>
                        </form>
                        {{-- Baris 2: Header --}}
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase" style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Nama Lengkap</th>
                            <th class="py-2 px-3">Username</th>
                            <th class="py-2 px-3">NIP</th>
                            <th class="py-2 px-3">Unit Kerja</th>
                            <th class="py-2 px-3">Posisi</th>
                            <th class="py-2 px-3">Profesi</th>
                            <th class="py-2 px-3">Jabatan</th>
                            <th class="py-2 px-3">Role</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr>
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                </td>
                                <td class="py-2 px-3 text-slate-600 font-monospace text-sm">{{ $user->username ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700 font-monospace text-sm">{{ $user->nip ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700">{{ $user->unit_kerja ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700">{{ $user->posisi_pekerjaan ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700">{{ $user->profesi ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700">{{ $user->jabatan ?? '-' }}</td>
                                <td class="py-2 px-3">
                                    @if($user->role === 'admin')
                                        <span class="badge-indigo">Admin</span>
                                    @elseif($user->role === 'driver')
                                        <span class="badge-amber">Driver</span>
                                    @else
                                        <span class="badge-slate">User</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <button type="button" class="btn-action-delete"
                                                onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-center text-slate-500 text-xs">Tidak ada data user</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="d-md-none">
            @forelse($users as $user)
                <div class="sp-card p-3 mb-2">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <h3 class="fw-600 text-slate-900 text-sm mb-0">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p class="text-xs text-slate-600 mt-1 mb-0">{{ $user->unit_kerja ?? '-' }}</p>
                        </div>
                        @if($user->role === 'admin')
                            <span class="badge-indigo">Admin</span>
                        @elseif($user->role === 'driver')
                            <span class="badge-amber">Driver</span>
                        @else
                            <span class="badge-slate">User</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-edit flex-fill">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </a>
                        <button type="button" class="btn-action-delete flex-fill"
                                onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}')">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="sp-card p-5 text-center text-slate-500 text-xs">
                    Tidak ada data user
                </div>
            @endforelse
            @if($users->hasPages())
                <div class="pt-2">{{ $users->links() }}</div>
            @endif
        </div>

    </div>

    <script>
        function confirmDelete(url) {
            if (!confirm('Yakin ingin menghapus user ini?')) return;
            const form = document.getElementById('delete-user-form');
            form.action = url;
            form.submit();
        }

        // Realtime filter
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('users-filter-form');
            if (!form) return;
            let timer;

            function submitClean() {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    form.querySelectorAll('input, select').forEach(el => {
                        if (el.value === '') el.disabled = true;
                    });
                    form.submit();
                }, 400);
            }

            form.querySelectorAll('input[type="text"]').forEach(el => {
                el.addEventListener('input', submitClean);
            });
            form.querySelectorAll('select').forEach(el => {
                el.addEventListener('change', () => {
                    clearTimeout(timer);
                    form.querySelectorAll('input, select').forEach(e => {
                        if (e.value === '') e.disabled = true;
                    });
                    form.submit();
                });
            });
        });
    </script>
</x-app-layout>
