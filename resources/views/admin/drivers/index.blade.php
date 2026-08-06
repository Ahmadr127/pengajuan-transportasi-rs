<x-app-layout title="Manajemen Driver — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Master Driver</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola data driver/pengemudi</p>
            </div>
            <a href="{{ route('admin.drivers.create') }}"
               class="btn btn-sp-primary d-inline-flex align-items-center gap-2 text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="white" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="fw-600">Tambah</span>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-sp-success border border-emerald-200 rounded px-3 py-2 mb-3 text-xs fw-500">
                {{ session('success') }}
            </div>
        @endif

        <!-- Desktop Table -->
        <form method="GET" action="{{ route('admin.drivers.index') }}" id="drivers-filter-form">
        <div class="d-none d-md-block sp-card overflow-hidden mb-3">
            <div class="overflow-x-auto">
                <table class="sp-table w-100 text-xs">
                    <thead>
                        {{-- Filter row --}}
                        <tr class="bg-slate-50 border-bottom border-slate-200">
                            <th class="py-2 px-3">
                                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="telepon" value="{{ request('telepon') }}" placeholder="Cari telepon..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="sim" value="{{ request('sim') }}" placeholder="Cari SIM..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <select name="is_active" class="form-select form-select-sm text-xxs border-slate-300">
                                    <option value="">Semua</option>
                                    <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                                    <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
                                </select>
                            </th>
                            <th class="py-2 px-3"></th>
                        </tr>
                        {{-- Header row --}}
                        <tr class="text-start text-xxs fw-600 text-white text-uppercase" style="background: linear-gradient(to right, #007774, #009e9a);">
                            <th class="py-2 px-3">Nama</th>
                            <th class="py-2 px-3">Telepon</th>
                            <th class="py-2 px-3">Nomor SIM</th>
                            <th class="py-2 px-3">Status</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($drivers as $driver)
                            <tr>
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-900">{{ $driver->name }}</div>
                                </td>
                                <td class="py-2 px-3 text-slate-700">{{ $driver->phone ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-700 font-monospace">{{ $driver->license_number ?? '-' }}</td>
                                <td class="py-2 px-3">
                                    @if($driver->is_active)
                                        <span class="badge-emerald">Aktif</span>
                                    @else
                                        <span class="badge-slate">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                                            class="btn-action-edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <button type="button" class="btn-action-delete"
                                                onclick="deleteDriver('{{ route('admin.drivers.destroy', $driver) }}')">
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
                                <td colspan="5" class="py-5 text-center text-slate-500 text-xs">Tidak ada data driver</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
                {{ $drivers->links() }}
            </div>
        </div>
        </form>

        {{-- Hidden delete form (outside filter form to avoid nesting) --}}
        <form id="delete-driver-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function deleteDriver(url) {
                if (!confirm('Yakin ingin menghapus driver ini?')) return;
                const form = document.getElementById('delete-driver-form');
                form.action = url;
                form.submit();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('drivers-filter-form');
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

        <!-- Mobile Cards -->
        <div class="d-md-none">
            @forelse($drivers as $driver)
                <div class="sp-card p-3 mb-2">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <h3 class="fw-600 text-slate-900 text-sm mb-0">{{ $driver->name }}</h3>
                            <p class="text-xs text-slate-600 mt-1 mb-0">{{ $driver->phone ?? '-' }}</p>
                            <p class="text-xs text-slate-500 mt-1 mb-0">SIM: {{ $driver->license_number ?? '-' }}</p>
                        </div>
                        @if($driver->is_active)
                            <span class="badge-emerald">Aktif</span>
                        @else
                            <span class="badge-slate">Nonaktif</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                        <a href="{{ route('admin.drivers.edit', $driver) }}"
                            class="btn-action-edit flex-fill">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" class="flex-fill"
                              onsubmit="return confirm('Yakin ingin menghapus driver ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn-action-delete w-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="sp-card p-5 text-center text-slate-500 text-xs">
                    Tidak ada data driver
                </div>
            @endforelse
            @if($drivers->hasPages())
                <div class="pt-2">{{ $drivers->links() }}</div>
            @endif
        </div>

    </div>
</x-app-layout>
