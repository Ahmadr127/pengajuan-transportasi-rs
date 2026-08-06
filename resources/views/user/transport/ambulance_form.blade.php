<x-app-layout title="Pengajuan Ambulans — SIPETRANS">
<div style="max-width:56rem;margin:0 auto;">

{{-- Error Alert --}}
@if ($errors->any())
<div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 mb-2">
    <svg class="flex-shrink-0 mt-0.5" width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
    </svg>
    <div>
        <div class="fw-600 mb-1">Periksa kembali data yang diisi:</div>
        <ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('pengajuan.ambulance.store') }}" id="ambulanceForm"
      x-data="{ sampaiSelesai: {{ old('sampai_selesai') ? 'true' : 'false' }} }">
    @csrf

    <div class="sp-card overflow-hidden">
        {{-- Accent + Header --}}
        <div style="height:.25rem;background:linear-gradient(to right,#007774,#81bd41);"></div>
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-slate-200" style="background:#f8fafc;">
            <div>
                <div class="fw-700 text-slate-800">Form Pengajuan Ambulance</div>
                <div class="text-xxs text-slate-500">Lengkapi data antar/jemput pasien</div>
            </div>
            <a href="{{ route('pengajuan.choose') }}" class="text-xxs fw-500 text-slate-500 text-decoration-none">← Kembali</a>
        </div>

        <div class="p-3">

            {{-- Row 1: Unit Kerja + Jenis Layanan --}}
            <div class="row g-2 mb-2">
                <div class="col-sm-5">
                    <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Unit Kerja</label>
                    <input value="{{ auth()->user()->unit_kerja ?? '-' }}" readonly
                        class="form-control form-control-sm bg-slate-50 text-slate-700 fw-500">
                </div>
                <div class="col-sm-7">
                    <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Jenis Layanan <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <label class="flex-fill d-flex align-items-center justify-content-center gap-1 border rounded cursor-pointer py-1 px-2 sp-radio-card" style="font-size:0.75rem;">
                            <input type="radio" name="purpose" value="antar" class="form-check-input m-0"
                                @checked(old('purpose')==='antar') onchange="updateAlamatForm('antar')">
                            <span class="fw-600">Antar</span>
                        </label>
                        <label class="flex-fill d-flex align-items-center justify-content-center gap-1 border rounded cursor-pointer py-1 px-2 sp-radio-card" style="font-size:0.75rem;">
                            <input type="radio" name="purpose" value="jemput" class="form-check-input m-0"
                                @checked(old('purpose')==='jemput') onchange="updateAlamatForm('jemput')">
                            <span class="fw-600">Jemput</span>
                        </label>
                    </div>
                    @error('purpose')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Row 2: Waktu --}}
            <div class="rounded border border-slate-200 p-2 mb-2" style="background:#f8fafc;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <span class="fw-600 text-slate-800">Waktu Penggunaan</span>
                    <label class="d-flex align-items-center gap-1 cursor-pointer select-none mb-0">
                        <input type="checkbox" name="sampai_selesai" value="1" x-model="sampaiSelesai"
                            class="form-check-input m-0" style="width:.875rem;height:.875rem;"
                            {{ old('sampai_selesai') ? 'checked' : '' }}>
                        <span class="text-xxs fw-600 text-slate-700">Sampai Selesai</span>
                    </label>
                </div>
                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Tgl Dari <span class="text-danger">*</span></label>
                        <input type="text" id="tanggal_display" placeholder="dd/mm/yyyy" autocomplete="off"
                            class="form-control form-control-sm cursor-pointer">
                        <input type="hidden" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                    </div>
                    <div class="col-6 col-sm-3">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Jam Dari <span class="text-danger">*</span></label>
                        <input type="text" name="jam" value="{{ old('jam') }}" placeholder="00:00" required
                            pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$" maxlength="5" inputmode="numeric"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-sm-3" x-show="!sampaiSelesai && true">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Tgl Sampai <span class="text-danger">*</span></label>
                        <input type="text" id="tanggal_sampai_display" placeholder="dd/mm/yyyy" autocomplete="off"
                            class="form-control form-control-sm cursor-pointer">
                        <input type="hidden" name="tanggal_sampai" id="tanggal_sampai" value="{{ old('tanggal_sampai', date('Y-m-d')) }}">
                    </div>
                    <div class="col-6 col-sm-3" x-show="!sampaiSelesai">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Jam Sampai <span class="text-danger">*</span></label>
                        <input type="text" name="jam_sampai" value="{{ old('jam_sampai') }}" placeholder="00:00"
                            pattern="^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$" maxlength="5" inputmode="numeric"
                            class="form-control form-control-sm">
                    </div>
                </div>

                {{-- Sampai selesai info --}}
                <div x-show="sampaiSelesai" class="mt-2 d-flex align-items-center gap-1 text-xxs text-teal-700 rounded px-2 py-1" style="background:#f0fdfa;border:1px solid #99f6e4;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Waktu selesai dicatat otomatis saat kendaraan kembali
                </div>

                {{-- Cek ketersediaan --}}
                <div class="mt-2 d-flex align-items-center gap-2">
                    <button id="checkAvailabilityBtn" type="button"
                        class="d-inline-flex align-items-center gap-1 rounded px-2 py-1 text-white fw-600 border-0"
                        style="font-size:.6875rem;background:#007774;">
                        <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Cek Ketersediaan
                    </button>
                    <span id="availabilityStatus" class="text-xxs fw-600"></span>
                </div>
            </div>

            {{-- Row 3: Prioritas + Alamat --}}
            <div class="row g-2 mb-2">
                <div class="col-sm-5">
                    <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Tingkat Kebutuhan <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <label class="flex-fill d-flex align-items-center gap-1 border rounded cursor-pointer px-2 py-1 sp-radio-card" style="font-size:.6875rem;">
                            <input type="radio" name="prioritas" value="segera" class="form-check-input m-0" @checked(old('prioritas')==='segera')>
                            <div class="flex-fill"><div class="fw-600">Cito</div><div class="text-xxs text-slate-500">Emergency</div></div>
                            <span class="badge-red">URGENT</span>
                        </label>
                        <label class="flex-fill d-flex align-items-center gap-1 border rounded cursor-pointer px-2 py-1 sp-radio-card" style="font-size:.6875rem;">
                            <input type="radio" name="prioritas" value="biasa" class="form-check-input m-0" @checked(old('prioritas')==='biasa')>
                            <div class="flex-fill"><div class="fw-600">Biasa</div><div class="text-xxs text-slate-500">Normal</div></div>
                            <span class="badge-cyan">NORMAL</span>
                        </label>
                    </div>
                    @error('prioritas')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-7" id="alamatSection">
                    <div id="alamatTujuanDiv" style="display:none;">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Alamat Tujuan <span class="text-danger">*</span></label>
                        <textarea name="alamat_tujuan" rows="3" class="form-control form-control-sm" placeholder="Alamat lengkap lokasi tujuan">{{ old('alamat_tujuan') }}</textarea>
                        @error('alamat_tujuan')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div id="alamatAsalDiv" style="display:none;">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Alamat Asal (Jemput) <span class="text-danger">*</span></label>
                        <textarea name="alamat_asal" rows="3" class="form-control form-control-sm" placeholder="Alamat lengkap lokasi penjemputan">{{ old('alamat_asal') }}</textarea>
                        @error('alamat_asal')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Row 4: Identitas Pasien --}}
            <div class="rounded border border-slate-200 p-2" style="background:#f8fafc;">
                <div class="fw-600 text-slate-800 mb-2">Identitas Pasien</div>
                <div class="row g-2">
                    <div class="col-sm-3">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">No. Rekam Medis</label>
                        <input name="pasien_no_rm" value="{{ old('pasien_no_rm') }}" placeholder="No. RM"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Nama Pasien <span class="text-danger">*</span></label>
                        <input name="pasien_nama" required value="{{ old('pasien_nama') }}" placeholder="Nama lengkap pasien"
                            class="form-control form-control-sm fw-500">
                        @error('pasien_nama')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-sm-5">
                        <label class="form-label text-xxs fw-600 text-slate-600 mb-1">Alamat Pasien <span class="text-danger">*</span></label>
                        <input name="alamat_pasien" required value="{{ old('alamat_pasien') }}" placeholder="Alamat lengkap pasien"
                            class="form-control form-control-sm">
                        @error('alamat_pasien')<div class="text-xxs text-danger mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

        </div>{{-- /p-3 --}}

        {{-- Footer --}}
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top border-slate-200" style="background:#f8fafc;">
            <a href="{{ route('pengajuan.choose') }}" class="text-xxs fw-500 text-slate-500 text-decoration-none">← Kembali</a>
            <button id="submitBtn" type="submit"
                class="d-inline-flex align-items-center gap-1 rounded px-3 py-1 text-white fw-600 border-0"
                style="font-size:.75rem;background:#007774;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                <span id="submitBtnText">Kirim Pengajuan</span>
            </button>
        </div>
    </div>
</form>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
function updateAlamatForm(purpose) {
    document.getElementById('alamatTujuanDiv').style.display = purpose === 'antar' ? 'block' : 'none';
    document.getElementById('alamatAsalDiv').style.display  = purpose === 'jemput' ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    const p = document.querySelector('input[name="purpose"]:checked');
    if (p) updateAlamatForm(p.value);
});

(function(){
    const btn = document.getElementById('checkAvailabilityBtn');
    const statusEl = document.getElementById('availabilityStatus');
    const submitBtn = document.getElementById('submitBtn');
    if (!btn) return;

    function isSampaiSelesai() { const c = document.querySelector('input[name="sampai_selesai"]'); return c && c.checked; }

    function resetCheck() {
        submitBtn.disabled = true;
        statusEl.textContent = 'Cek ketersediaan diperlukan';
        statusEl.className = 'text-xxs fw-600 text-amber-600';
    }

    const form = btn.closest('form');
    ['tanggal','jam','tanggal_sampai','jam_sampai'].forEach(n => {
        const el = form.querySelector(`[name="${n}"]`);
        if (el) { el.addEventListener('change', resetCheck); el.addEventListener('input', resetCheck); }
    });
    form.querySelector('input[name="sampai_selesai"]')?.addEventListener('change', resetCheck);

    btn.addEventListener('click', async function() {
        const tanggal = form.querySelector('input[name="tanggal"]').value;
        const jam = form.querySelector('input[name="jam"]').value;
        const ss = isSampaiSelesai();
        const tanggal_sampai = ss ? tanggal : form.querySelector('input[name="tanggal_sampai"]').value;
        const jam_sampai = ss ? '23:59' : form.querySelector('input[name="jam_sampai"]').value;

        if (!tanggal || !jam || !tanggal_sampai || !jam_sampai) {
            statusEl.textContent = 'Lengkapi tanggal dan jam terlebih dahulu';
            statusEl.className = 'text-xxs fw-600 text-amber-600';
            return;
        }
        statusEl.textContent = 'Memeriksa…';
        statusEl.className = 'text-xxs fw-600 text-slate-600';

        const url = new URL('{{ route('pengajuan.ambulance.check') }}', window.location.origin);
        url.searchParams.set('tanggal', tanggal);
        url.searchParams.set('jam', jam);
        url.searchParams.set('tanggal_sampai', tanggal_sampai);
        url.searchParams.set('jam_sampai', jam_sampai);

        try {
            const data = await fetch(url.toString(), { credentials: 'same-origin' }).then(r => r.json());
            if (data.available) {
                statusEl.textContent = `✓ Tersedia (${data.available_units}/${data.total_units} unit)`;
                statusEl.className = 'text-xxs fw-600 text-emerald-600';
                submitBtn.disabled = false;
            } else {
                statusEl.textContent = `✗ Tidak tersedia (${data.available_units}/${data.total_units})`;
                statusEl.className = 'text-xxs fw-600 text-danger';
                submitBtn.disabled = true;
            }
        } catch { statusEl.textContent = '⚠ Terjadi kesalahan'; statusEl.className = 'text-xxs fw-600 text-danger'; }
    });
    resetCheck();
})();

function initFlatpickr() {
    if (typeof flatpickr === 'undefined') { setTimeout(initFlatpickr, 50); return; }
    const cfg = { dateFormat:'Y-m-d', altInput:true, altFormat:'d/m/Y', allowInput:false, minDate:'today', locale:{firstDayOfWeek:1} };
    flatpickr('#tanggal_display', { ...cfg, defaultDate: document.getElementById('tanggal').value || new Date(),
        onChange(_, d) { document.getElementById('tanggal').value = d; document.getElementById('tanggal').dispatchEvent(new Event('change'));
            const fp = document.getElementById('tanggal_sampai_display')?._flatpickr; if (fp) fp.set('minDate', d); }
    });
    flatpickr('#tanggal_sampai_display', { ...cfg, defaultDate: document.getElementById('tanggal_sampai').value || new Date(),
        onChange(_, d) { document.getElementById('tanggal_sampai').value = d; document.getElementById('tanggal_sampai').dispatchEvent(new Event('change')); }
    });
}

document.getElementById('ambulanceForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    if (btn.dataset.submitted === '1') { e.preventDefault(); return; }
    btn.dataset.submitted = '1'; btn.disabled = true;
    document.getElementById('submitBtnText').textContent = 'Mengirim…';
});

document.addEventListener('DOMContentLoaded', function() {
    initFlatpickr();

    function getMinJam(tanggalVal) {
        const t = new Date(), ts = t.toISOString().slice(0,10);
        if (tanggalVal === ts) return String(t.getHours()).padStart(2,'0')+':'+String(t.getMinutes()).padStart(2,'0');
        return null;
    }
    function showJamError(inp, msg) {
        clearJamError(inp); inp.classList.add('is-invalid');
        const e = document.createElement('div'); e.className = 'jam-error-msg text-xxs text-danger mt-1'; e.textContent = msg;
        inp.parentNode.appendChild(e);
    }
    function clearJamError(inp) {
        inp.classList.remove('is-invalid');
        inp.parentNode.querySelector('.jam-error-msg')?.remove();
    }
    function setupTimeInput(el) {
        el.addEventListener('input', function() {
            let v = el.value.replace(/[^0-9]/g,'');
            if (v.length > 2) v = v.slice(0,2)+':'+v.slice(2,4);
            el.value = v;
        });
        el.addEventListener('blur', function() {
            let v = el.value;
            if (v.length===4 && !v.includes(':')) v = v.slice(0,2)+':'+v.slice(2,4);
            if (v.includes(':')) {
                const [h,m] = v.split(':');
                el.value = String(Math.min(parseInt(h)||0,23)).padStart(2,'0')+':'+String(Math.min(parseInt(m)||0,59)).padStart(2,'0');
            }
        });
        el.addEventListener('keypress', e => { if (!/[0-9]/.test(e.key)) e.preventDefault(); });
    }
    function enforceMinJam(jamInput, tanggalId) {
        const tanggalHidden = document.getElementById(tanggalId);
        jamInput.addEventListener('blur', function() {
            const minJam = getMinJam(tanggalHidden?.value || '');
            if (minJam && jamInput.value && jamInput.value < minJam) {
                showJamError(jamInput, 'Jam tidak boleh kurang dari jam sekarang'); return;
            } else { clearJamError(jamInput); }
            if (jamInput.name === 'jam_sampai') {
                const jd = document.querySelector('input[name="jam"]');
                const td = document.getElementById('tanggal'), ts = document.getElementById('tanggal_sampai');
                if (jd && td && ts && td.value === ts.value && jamInput.value && jd.value && jamInput.value <= jd.value)
                    showJamError(jamInput, 'Jam sampai harus lebih besar dari jam dari');
                else clearJamError(jamInput);
            }
        });
        jamInput.addEventListener('input', () => clearJamError(jamInput));
    }
    ['jam','jam_sampai'].forEach(n => {
        const inp = document.querySelector(`input[name="${n}"]`);
        if (inp) { setupTimeInput(inp); enforceMinJam(inp, n==='jam'?'tanggal':'tanggal_sampai'); }
    });
});
</script>

</div>{{-- /max-width wrapper --}}
</x-app-layout>
