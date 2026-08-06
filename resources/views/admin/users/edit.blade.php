<x-app-layout title="Edit Pengguna — SIPETRANS">
    <div class="px-3 pt-3 pb-4" style="max-width:48rem;margin:0 auto;"
         x-data="Object.assign(usernameGenEdit('{{ old('nama_lengkap', $user->full_name) }}', '{{ $user->username }}'), { editRole: '{{ old('role', $user->role) }}', editPriorityLevel: '{{ old('priority_level', $user->priority_level ?? 0) }}' })">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('admin.users.index') }}"
               class="btn btn-sp-outline d-inline-flex align-items-center gap-1 text-xs fw-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-sm fw-bold text-slate-800 mb-0">Edit User — <span class="text-slate-500 fw-normal">{{ $user->full_name }}</span></h1>
        </div>

        <div class="sp-card p-4">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Baris 1: Nama + Username --}}
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" x-model="namaLengkap" @input="generateUsername()" required
                               class="form-control form-control-sm">
                        <p class="mt-1 text-xxs text-slate-400">Gelar belakang dipisah koma. Contoh: Budi, S.Kom.</p>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Username (otomatis)</label>
                        <input type="text" readonly :value="username"
                               class="form-control form-control-sm bg-slate-50 text-slate-500 font-monospace" style="cursor:not-allowed;">
                        <p class="mt-1 text-xxs text-slate-400">Dibuat otomatis dari nama</p>
                    </div>
                </div>

                {{-- Baris 2: NIP + Password --}}
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $user->nip) }}" placeholder="Nomor Induk Pegawai"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Password</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                               class="form-control form-control-sm @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback text-xxs">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Baris 3: Unit Kerja + Posisi --}}
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Unit Kerja</label>
                        <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $user->unit_kerja) }}"
                               class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Posisi Pekerjaan</label>
                        <input type="text" name="posisi_pekerjaan" value="{{ old('posisi_pekerjaan', $user->posisi_pekerjaan) }}"
                               class="form-control form-control-sm">
                    </div>
                </div>

                {{-- Baris 4: Profesi + Jabatan --}}
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Profesi</label>
                        <select name="profesi" class="form-select form-select-sm">
                            <option value="">-- Pilih Profesi --</option>
                            <option value="MEDIS" {{ old('profesi', $user->profesi) == 'MEDIS' ? 'selected' : '' }}>MEDIS</option>
                            <option value="NON MEDIS" {{ old('profesi', $user->profesi) == 'NON MEDIS' ? 'selected' : '' }}>NON MEDIS</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
                               class="form-control form-control-sm">
                    </div>
                </div>

                {{-- Baris 5: Role + Level Prioritas --}}
                <div class="row g-3 mb-2">
                    <div class="col-6">
                        <label class="form-label text-xxs fw-600 text-slate-600">Role *</label>
                        <select name="role" x-model="editRole" required
                                class="form-select form-select-sm">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <div class="col-6" x-show="editRole === 'user'">
                        <label class="form-label text-xxs fw-600 text-slate-600">Level Prioritas</label>
                        <select name="priority_level" x-model="editPriorityLevel"
                                class="form-select form-select-sm">
                            <option value="0">Normal</option>
                            <option value="1">Prioritas Tinggi</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top border-slate-100">
                    <button type="submit" class="btn btn-sp-primary text-xs fw-600">Update</button>
                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-sp-outline text-xs fw-600">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function usernameGenEdit(initialNama, initialUsername) {
            return {
                namaLengkap: initialNama,
                username: initialUsername,
                generateUsername() {
                    const raw = (this.namaLengkap.split(',')[0] || '').trim();
                    if (!raw) { this.username = ''; return; }
                    const parts = raw.split(/\s+/);
                    const w1 = (parts[0] || '').toLowerCase().replace(/[^a-z0-9]/g, '');
                    const w2raw = parts[1] ? parts[1].toLowerCase().replace(/[^a-z0-9]/g, '') : null;
                    this.username = (w2raw && w2raw !== '') ? `${w1}.${w2raw}` : w1;
                }
            }
        }
    </script>
</x-app-layout>
