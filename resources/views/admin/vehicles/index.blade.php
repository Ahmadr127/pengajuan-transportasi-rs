<x-app-layout title="Manajemen Kendaraan — SIPETRANS">
    <div class="container-fluid px-3 pt-4 pb-6">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Master Kendaraan</h1>
                <p class="text-slate-500 text-xs mt-1 mb-0">Kelola data kendaraan transportasi</p>
            </div>
            <a href="{{ route('admin.vehicles.create') }}"
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
        <form method="GET" action="{{ route('admin.vehicles.index') }}" id="vehicles-filter-form">
        <div class="d-none d-lg-block sp-card overflow-hidden mb-3">
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
                                <select name="type" class="form-select form-select-sm text-xxs border-slate-300">
                                    <option value="">Semua</option>
                                    <option value="umum" @selected(request('type') === 'umum')>Umum</option>
                                    <option value="ambulance" @selected(request('type') === 'ambulance')>Ambulance</option>
                                </select>
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="plat" value="{{ request('plat') }}" placeholder="Cari plat..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3">
                                <input type="text" name="merk" value="{{ request('merk') }}" placeholder="Cari merk..."
                                       class="form-control form-control-sm text-xxs border-slate-300">
                            </th>
                            <th class="py-2 px-3"></th>
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
                            <th class="py-2 px-3">Nama Unit</th>
                            <th class="py-2 px-3">Jenis</th>
                            <th class="py-2 px-3">Plat Nomor</th>
                            <th class="py-2 px-3">Merk/Model</th>
                            <th class="py-2 px-3">KM Terkini</th>
                            <th class="py-2 px-3">Status</th>
                            <th class="py-2 px-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="py-2 px-3">
                                    <div class="fw-600 text-slate-900">{{ $vehicle->name }}</div>
                                </td>
                                <td class="py-2 px-3">
                                    @if($vehicle->type === 'ambulance')
                                        <span class="badge-red">Ambulance</span>
                                    @else
                                        <span class="badge-blue">Umum</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-slate-700 font-monospace fw-500">{{ $vehicle->plate_number }}</td>
                                <td class="py-2 px-3 text-slate-700">
                                    {{ $vehicle->brand ?? '-' }}
                                    @if($vehicle->model)<span class="text-slate-500">/ {{ $vehicle->model }}</span>@endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($vehicle->last_km !== null)
                                        <span class="fw-600 text-slate-800">{{ number_format($vehicle->last_km, 0, ',', '.') }}</span>
                                        <span class="text-slate-400 text-xxs"> km</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3">
                                    @if($vehicle->is_active)
                                        <span class="badge-emerald">Aktif</span>
                                    @else
                                        <span class="badge-slate">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                                            class="btn-action-edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <button type="button" class="btn-action-delete"
                                                onclick="deleteVehicle('{{ route('admin.vehicles.destroy', $vehicle) }}')">
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
                                <td colspan="7" class="py-5 text-center text-slate-500 text-xs">Tidak ada data kendaraan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 bg-slate-50 border-top border-slate-200">
                {{ $vehicles->links() }}
            </div>
        </div>
        </form>

        {{-- Hidden delete form (outside filter form to avoid nesting) --}}
        <form id="delete-vehicle-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
            function deleteVehicle(url) {
                if (!confirm('Yakin ingin menghapus kendaraan ini?')) return;
                const form = document.getElementById('delete-vehicle-form');
                form.action = url;
                form.submit();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('vehicles-filter-form');
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
        <div class="d-lg-none">
            @forelse($vehicles as $vehicle)
                <div class="sp-card p-3 mb-2">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h3 class="fw-600 text-slate-900 text-sm mb-0">{{ $vehicle->name }}</h3>
                                @if($vehicle->type === 'ambulance')
                                    <span class="badge-red">Ambulance</span>
                                @else
                                    <span class="badge-blue">Umum</span>
                                @endif
                            </div>
                            <p class="text-xs fw-500 text-slate-700 mb-0">{{ $vehicle->plate_number }}</p>
                            <p class="text-xs text-slate-600 mt-1 mb-0">
                                {{ $vehicle->brand ?? '-' }}
                                @if($vehicle->model) / {{ $vehicle->model }}@endif
                                @if($vehicle->year) ({{ $vehicle->year }})@endif
                            </p>
                            @if($vehicle->last_km !== null)
                                <p class="text-xs text-slate-700 mt-1 mb-0 fw-500">
                                    KM Terkini: <span class="fw-600">{{ number_format($vehicle->last_km, 0, ',', '.') }} km</span>
                                </p>
                            @endif
                        </div>
                        @if($vehicle->is_active)
                            <span class="badge-emerald">Aktif</span>
                        @else
                            <span class="badge-slate">Nonaktif</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                        <a href="{{ route('admin.vehicles.edit', $vehicle) }}"
                            class="btn-action-edit flex-fill">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="flex-fill"
                              onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
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
                    Tidak ada data kendaraan
                </div>
            @endforelse
            @if($vehicles->hasPages())
                <div class="pt-2">{{ $vehicles->links() }}</div>
            @endif
        </div>

    </div>
</x-app-layout>
