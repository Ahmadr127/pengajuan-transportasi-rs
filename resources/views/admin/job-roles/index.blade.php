<x-app-layout title="Master Jabatan — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Master Jabatan</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola data jabatan pegawai (dari Simutu)</p>
            </div>
            @can('jabatan.create')
            <a href="{{ route('admin.job-roles.create') }}"
               class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="fw-600">Tambah</span>
            </a>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.job-roles.index') }}" id="job-roles-filter-form">
            <div class="sp-card overflow-hidden mb-3">
                <div class="overflow-x-auto">
                    <table class="sp-table w-100 text-xs">
                        <thead>
                            {{-- Filter row --}}
                            <tr class="bg-slate-50 border-bottom border-slate-200">
                                <th class="py-2 px-3">
                                    <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari jabatan..."
                                           class="form-control form-control-sm text-xxs border-slate-300">
                                </th>
                                <th class="py-2 px-3"></th>
                                <th class="py-2 px-3"></th>
                            </tr>
                            {{-- Header row --}}
                            <tr class="text-start text-xxs fw-600 text-white text-uppercase" style="background: linear-gradient(to right, #007774, #009e9a);">
                                <th class="py-2 px-3">Nama Jabatan</th>
                                <th class="py-2 px-3">Deskripsi</th>
                                <th class="py-2 px-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($jobRoles as $jobRole)
                                <tr>
                                    <td class="py-2 px-3"><div class="fw-600 text-slate-900">{{ $jobRole->nama_role }}</div></td>
                                    <td class="py-2 px-3 text-slate-600">{{ $jobRole->deskripsi_role ?? '-' }}</td>
                                    <td class="py-2 px-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            @can('jabatan.edit')
                                            <a href="{{ route('admin.job-roles.edit', $jobRole) }}" class="btn-action-edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                                Edit
                                            </a>
                                            @endcan
                                            @can('jabatan.delete')
                                            <button type="button" class="btn-action-delete"
                                                    onclick="deleteJobRole('{{ route('admin.job-roles.destroy', $jobRole) }}')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-5 text-center text-slate-500 text-xs">Tidak ada data jabatan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
                    {{ $jobRoles->links() }}
                </div>
            </div>
        </form>

        <form id="delete-job-role-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function deleteJobRole(url) {
                if (!confirm('Yakin ingin menghapus jabatan ini?')) return;
                const form = document.getElementById('delete-job-role-form');
                form.action = url;
                form.submit();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('job-roles-filter-form');
                if (!form) return;
                let timer;
                form.querySelectorAll('input[type="text"]').forEach(el => {
                    el.addEventListener('input', () => {
                        clearTimeout(timer);
                        timer = setTimeout(() => form.submit(), 400);
                    });
                });
            });
        </script>

    </div>
</x-app-layout>
