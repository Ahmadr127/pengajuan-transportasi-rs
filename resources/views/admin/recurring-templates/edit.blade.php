<x-app-layout title="Edit Template Pengajuan Berulang — SIPETRANS">
    <div class="px-3 py-4" style="max-width:42rem;margin:0 auto;">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('admin.recurring-templates.index') }}"
               class="d-inline-flex align-items-center justify-content-center rounded-circle bg-slate-100 text-slate-500 hover-bg-slate-200 transition"
               style="width:32px;height:32px;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="fw-bold text-slate-800 mb-0" style="font-size:1.1rem;">Edit Template Berulang</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-sp-danger mb-4 text-sm">
                <ul class="list-disc ms-3 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="sp-card overflow-hidden">
            <div class="p-3 border-bottom border-slate-100 bg-slate-50 d-flex flex-wrap gap-4">
                <div>
                    <span class="text-xs text-slate-500">Pemohon:</span>
                    <span class="text-xs fw-600 text-slate-700">{{ $recurring_template->pemohon_nama }} <span class="text-slate-500 fw-normal">({{ $recurring_template->pemohon_unit }})</span></span>
                </div>
                <div>
                    <span class="text-xs text-slate-500">Jenis:</span>
                    <span class="text-xs fw-600 text-slate-700">{{ ucfirst($recurring_template->jenis) }} <span class="text-slate-500 fw-normal">— {{ \Illuminate\Support\Str::limit($recurring_template->keperluan, 30) }}</span></span>
                </div>
                <div>
                    <span class="text-xs text-slate-500">Waktu:</span>
                    <span class="text-xs fw-600 text-slate-700" id="preview-waktu">{{ substr($recurring_template->jam, 0, 5) }} {{ $recurring_template->jam_sampai ? '- '.substr($recurring_template->jam_sampai, 0, 5) : '- Selesai' }}</span>
                </div>
            </div>

            <form action="{{ route('admin.recurring-templates.update', $recurring_template) }}" method="POST" class="p-3" x-data="{ sampaiSelesai: {{ $recurring_template->jam_sampai ? 'false' : 'true' }} }">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label text-xs fw-600 text-slate-700 mb-1">Jam Berangkat <span class="text-danger">*</span></label>
                        <input type="text" name="jam" id="jam_input"
                            value="{{ old('jam', substr($recurring_template->jam, 0, 5)) }}"
                            placeholder="00:00" maxlength="5" inputmode="numeric"
                            pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                            class="form-control form-control-sm"
                            required>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label text-xs fw-600 text-slate-700 mb-0">Jam Selesai</label>
                            <div class="form-check m-0 d-flex align-items-center gap-1">
                                <input type="checkbox" name="sampai_selesai" value="1" x-model="sampaiSelesai"
                                    class="form-check-input m-0" style="width:0.85rem;height:0.85rem;"
                                    id="sampai_selesai_cb"
                                    {{ old('sampai_selesai', !$recurring_template->jam_sampai) ? 'checked' : '' }}>
                                <label class="form-check-label text-xxs fw-600 text-slate-600" for="sampai_selesai_cb">
                                    Seharian
                                </label>
                            </div>
                        </div>
                        <div x-show="!sampaiSelesai">
                            <input type="text" name="jam_sampai" id="jam_sampai_input"
                                value="{{ old('jam_sampai', $recurring_template->jam_sampai ? substr($recurring_template->jam_sampai, 0, 5) : '') }}"
                                placeholder="00:00" maxlength="5" inputmode="numeric"
                                pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$"
                                class="form-control form-control-sm">
                        </div>
                        <div x-show="sampaiSelesai"
                             class="form-control form-control-sm bg-slate-50 text-slate-500 text-xs d-flex align-items-center"
                             style="height:31px;">
                            Dicatat saat kembali
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-xs fw-600 text-slate-700 mb-1">Hari Aktif</label>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $days = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                            $currentDays = old('hari', $recurring_template->hari ?? []);
                        @endphp
                        @foreach($days as $num => $day)
                            <label class="d-flex align-items-center gap-1 px-2 py-1 rounded border border-slate-200" style="cursor:pointer;background:#fff;">
                                <input type="checkbox" name="hari[]" value="{{ $num }}"
                                    class="form-check-input m-0" style="width:0.85rem;height:0.85rem;"
                                    {{ in_array($num, $currentDays) ? 'checked' : '' }}>
                                <span class="text-xs fw-500 text-slate-700">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-xs fw-600 text-slate-700 mb-1">Status Template</label>
                    <div class="d-flex gap-2">
                        <label class="d-flex align-items-center gap-1 px-3 py-1.5 rounded border border-slate-200" style="cursor:pointer;background:#fff;">
                            <input type="radio" name="is_active" value="1"
                                class="form-check-input m-0" style="width:0.85rem;height:0.85rem;"
                                {{ old('is_active', $recurring_template->is_active) == 1 ? 'checked' : '' }}>
                            <span class="text-xs fw-600 text-slate-800">Aktif</span>
                        </label>
                        <label class="d-flex align-items-center gap-1 px-3 py-1.5 rounded border border-slate-200" style="cursor:pointer;background:#fff;">
                            <input type="radio" name="is_active" value="0"
                                class="form-check-input m-0" style="width:0.85rem;height:0.85rem;"
                                {{ old('is_active', $recurring_template->is_active) == 0 ? 'checked' : '' }}>
                            <span class="text-xs fw-600 text-slate-800">Nonaktif</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2 d-flex justify-content-end">
                    <button type="submit"
                            class="btn btn-sm d-inline-flex align-items-center gap-1.5 text-xs fw-600 text-white px-3 py-1.5"
                            style="background: linear-gradient(to right, #007774, #009e9a);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jamInput      = document.getElementById('jam_input');
            const jamSampaiInput = document.getElementById('jam_sampai_input');
            const sampaiSelesaiCb = document.querySelector('input[name="sampai_selesai"]');
            const previewWaktu  = document.getElementById('preview-waktu');

            // Auto-format HH:MM saat mengetik
            function setupTimeInput(el) {
                el.addEventListener('input', function () {
                    let v = el.value.replace(/[^0-9]/g, '');
                    if (v.length > 2) v = v.slice(0, 2) + ':' + v.slice(2, 4);
                    el.value = v;
                });
                el.addEventListener('blur', function () {
                    let v = el.value;
                    if (v.length === 4 && !v.includes(':')) v = v.slice(0, 2) + ':' + v.slice(2, 4);
                    if (v.includes(':')) {
                        let parts = v.split(':');
                        let h = Math.min(parseInt(parts[0]) || 0, 23);
                        let m = Math.min(parseInt(parts[1]) || 0, 59);
                        el.value = (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
                    }
                    updatePreview();
                });
                el.addEventListener('keypress', function (e) {
                    if (!/[0-9]/.test(e.key)) e.preventDefault();
                });
                el.addEventListener('input', updatePreview);
            }

            function updatePreview() {
                const jam = jamInput.value || '--:--';
                const selesai = sampaiSelesaiCb && sampaiSelesaiCb.checked;
                const jamSampai = selesai ? 'Selesai' : (jamSampaiInput && jamSampaiInput.value ? jamSampaiInput.value : 'Selesai');
                previewWaktu.textContent = jam + ' - ' + jamSampai;
            }

            if (jamInput) setupTimeInput(jamInput);
            if (jamSampaiInput) setupTimeInput(jamSampaiInput);
            if (sampaiSelesaiCb) sampaiSelesaiCb.addEventListener('change', updatePreview);
        });
    </script>
</x-app-layout>
