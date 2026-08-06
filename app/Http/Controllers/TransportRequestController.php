<?php

namespace App\Http\Controllers;

use App\Models\TransportRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransportRequestController extends Controller
{
    public function choose()
    {
        return view('user.transport.choose');
    }

    public function index(Request $request)
    {
        $query = $request->user()->transportRequests()->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $items = $query->paginate(10)->withQueryString();

        return view('user.transport.index', compact('items'));
    }

    public function createUmum()
    {
        return view('user.transport.umum_form');
    }

    public function storeUmum(Request $request)
    {
        $sampaiSelesai = $request->boolean('sampai_selesai');
        $isRecurring = $request->boolean('is_recurring');

        $data = $request->validate([
            'tanggal' => [$isRecurring ? 'nullable' : 'required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ($sampaiSelesai || $isRecurring) ? ['nullable', 'date'] : ['required', 'date'],
            'jam_sampai' => $sampaiSelesai ? ['nullable'] : ['required', 'date_format:H:i'],
            'prioritas' => ['required', 'in:segera,biasa'],
            'alamat_tujuan' => ['required', 'string', 'max:2000'],
            'keperluan' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($isRecurring) {
            $recurringData = $request->validate([
                'recurring_hari' => ['required', 'array', 'min:1'],
                'recurring_hari.*' => ['integer', 'min:1', 'max:7'],
            ]);

            $template = \App\Models\RecurringTransportTemplate::create([
                'user_id' => $request->user()->id,
                'jenis' => 'umum',
                'keperluan' => $data['keperluan'],
                'prioritas' => $data['prioritas'],
                'pemohon_nama' => $request->user()->full_name,
                'pemohon_unit' => $request->user()->unit_kerja,
                'jumlah_penumpang' => null,
                'jam' => $data['jam'],
                'jam_sampai' => $data['jam_sampai'] ?? null,
                'hari' => $recurringData['recurring_hari'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => '2099-12-31',
                'alamat_tujuan' => $data['alamat_tujuan'],
                'keterangan' => $data['keterangan'] ?? null,
                'is_active' => true,
            ]);

            $today = \Carbon\Carbon::today();
            $dayOfWeek = (int) $today->format('N');
            $currentTime = \Carbon\Carbon::now()->format('H:i');
            
            if (in_array($dayOfWeek, $recurringData['recurring_hari']) && substr($data['jam'], 0, 5) > $currentTime) {
                \App\Models\TransportRequest::create([
                    'user_id' => $template->user_id,
                    'jenis' => $template->jenis,
                    'nomor_pengajuan' => \App\Models\TransportRequest::generateNomor(),
                    'tanggal' => $today->format('Y-m-d'),
                    'jam' => substr($template->jam, 0, 5),
                    'tanggal_sampai' => $today->format('Y-m-d'),
                    'jam_sampai' => $template->jam_sampai ? substr($template->jam_sampai, 0, 5) : null,
                    'keperluan' => $template->keperluan,
                    'prioritas' => $template->prioritas,
                    'pemohon_nama' => $template->pemohon_nama,
                    'pemohon_unit' => $template->pemohon_unit,
                    'jumlah_penumpang' => $template->jumlah_penumpang,
                    'alamat_asal' => $template->alamat_asal,
                    'alamat_tujuan' => $template->alamat_tujuan,
                    'keterangan' => $template->keterangan,
                    'pasien_nama' => $template->pasien_nama,
                    'pasien_no_rm' => $template->pasien_no_rm,
                    'kontak' => $request->user()->phone ?? '',
                    'signature_pemohon' => \Illuminate\Support\Str::random(32),
                    'signature_pemohon_at' => now(),
                ]);
            }

            return redirect()->route('pengajuan.index')
                ->with('recurring_success', 'Template pengajuan berulang berhasil dibuat dan akan dijalankan secara otomatis.');
        }

        // Cegah double submission: cek apakah ada pengajuan identik dalam 10 detik terakhir
        $duplicate = TransportRequest::where('user_id', $request->user()->id)
            ->where('jenis', 'umum')
            ->where('tanggal', $data['tanggal'])
            ->where('jam', $data['jam'])
            ->where('keperluan', $data['keperluan'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicate) {
            return redirect()->back()->withInput()
                ->withErrors(['duplicate' => 'Pengajuan yang sama sudah dikirim. Silakan tunggu sebentar.']);
        }

        if ($sampaiSelesai) {
            $data['tanggal_sampai'] = null;
            $data['jam_sampai'] = null;
        }

        $data['kontak'] = $request->user()->phone ?? '';

        if (!$sampaiSelesai) {
            $mulai = Carbon::parse($data['tanggal'].' '.$data['jam']);
            $sampai = Carbon::parse($data['tanggal_sampai'].' '.$data['jam_sampai']);
            if ($sampai->lte($mulai)) {
                $sampai->addDay();
                $data['tanggal_sampai'] = Carbon::parse($data['tanggal_sampai'])->addDay()->toDateString();
            }
        }

        $transportRequest = TransportRequest::create([
            'user_id' => $request->user()->id,
            'jenis' => 'umum',
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'unit_mobil' => null,
            'keperluan' => $data['keperluan'],
            'prioritas' => $data['prioritas'],
            'pemohon_nama' => $request->user()->full_name,
            'pemohon_unit' => $request->user()->unit_kerja,
            'jumlah_penumpang' => null,
            // Auto-sign pemohon
            'signature_pemohon' => \Illuminate\Support\Str::random(32),
            'signature_pemohon_at' => now(),
            ...collect($data)->except(['keperluan', 'prioritas'])->all(),
        ]);

        return redirect()->route('pengajuan.success', $transportRequest)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function checkAvailability(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ['required', 'date'],
            'jam_sampai' => ['required', 'date_format:H:i'],
        ]);

        // User prioritas tinggi selalu tersedia
        if (auth()->user()->isPriority()) {
            $totalUnits = \App\Models\Vehicle::where('type', 'umum')->where('is_active', true)->count();
            return response()->json([
                'available' => true,
                'total_units' => $totalUnits,
                'used_units' => 0,
                'available_units' => $totalUnits,
            ]);
        }

        // Jika sampai_selesai, gunakan akhir hari
        if (empty($data['tanggal_sampai']) || empty($data['jam_sampai'])) {
            $data['tanggal_sampai'] = $data['tanggal'];
            $data['jam_sampai'] = '23:59';
        }

        $mulai = Carbon::parse($data['tanggal'].' '.$data['jam']);
        $sampai = Carbon::parse($data['tanggal_sampai'].' '.$data['jam_sampai']);
        if ($sampai->lte($mulai)) $sampai->addDay();

        $totalUnits = \App\Models\Vehicle::where('type', 'umum')->where('is_active', true)->count();

        // Hitung pengajuan yang overlap pada rentang waktu ini dengan status disetujui atau sedang digunakan
        $conflicting = TransportRequest::where('jenis', 'umum')
            ->whereIn('status', ['diproses', 'digunakan'])
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                // null = sampai selesai (seharian penuh hingga 23:59)
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : Carbon::parse($r->tanggal->format('Y-m-d').' 23:59');
                if ($rSampai->lte($rMulai)) $rSampai->addDay();
                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            });

        // Hitung unit unik yang sudah di-assign; sisanya dihitung per pengajuan tanpa unit
        $assignedUnits = $conflicting->whereNotNull('unit_mobil')->pluck('unit_mobil')->unique()->count();
        $unassignedCount = $conflicting->whereNull('unit_mobil')->count();
        $usedUnits = $assignedUnits + $unassignedCount;

        $available = $usedUnits < $totalUnits;

        return response()->json([
            'available' => $available,
            'total_units' => $totalUnits,
            'used_units' => $usedUnits,
            'available_units' => max(0, $totalUnits - $usedUnits),
        ]);
    }

    public function createAmbulance()
    {
        $purpose = old('purpose', 'antar');
        return view('user.transport.ambulance_form', compact('purpose'));
    }

    public function storeAmbulance(Request $request)
    {
        $sampaiSelesai = $request->boolean('sampai_selesai');
        $isRecurring = $request->boolean('is_recurring');

        $data = $request->validate([
            'purpose' => ['required', 'in:antar,jemput'],
            'pasien_nama' => ['required', 'string', 'max:255'],
            'pasien_no_rm' => ['nullable', 'string', 'max:50'],
            'alamat_pasien' => ['required', 'string', 'max:2000'],
            'tanggal' => [$isRecurring ? 'nullable' : 'required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ($sampaiSelesai || $isRecurring) ? ['nullable', 'date'] : ['required', 'date'],
            'jam_sampai' => $sampaiSelesai ? ['nullable'] : ['required', 'date_format:H:i'],
            'prioritas' => ['required', 'in:segera,biasa'],
            'alamat_tujuan' => ['nullable', 'string', 'max:2000'],
            'alamat_asal' => ['nullable', 'string', 'max:2000'],
        ]);

        $purpose = $data['purpose'];
        if ($purpose === 'antar' && empty($data['alamat_tujuan'])) {
            throw ValidationException::withMessages(['alamat_tujuan' => 'Alamat tujuan harus diisi.']);
        }
        if ($purpose === 'jemput' && empty($data['alamat_asal'])) {
            throw ValidationException::withMessages(['alamat_asal' => 'Alamat asal harus diisi.']);
        }

        $alamatAsal = $purpose === 'antar' ? 'RS' : ($data['alamat_asal'] ?? 'RS');
        $alamatTujuan = $purpose === 'antar' ? ($data['alamat_tujuan'] ?? 'RS') : 'RS';

        if ($isRecurring) {
            $recurringData = $request->validate([
                'recurring_hari' => ['required', 'array', 'min:1'],
                'recurring_hari.*' => ['integer', 'min:1', 'max:7'],
            ]);

            $template = \App\Models\RecurringTransportTemplate::create([
                'user_id' => $request->user()->id,
                'jenis' => 'ambulance',
                'keperluan' => $purpose,
                'prioritas' => $data['prioritas'],
                'pemohon_nama' => $request->user()->full_name,
                'pemohon_unit' => $request->user()->unit_kerja,
                'jumlah_penumpang' => null,
                'jam' => $data['jam'],
                'jam_sampai' => $data['jam_sampai'] ?? null,
                'hari' => $recurringData['recurring_hari'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => '2099-12-31',
                'alamat_asal' => $alamatAsal,
                'alamat_tujuan' => $alamatTujuan,
                'keterangan' => null,
                'pasien_nama' => $data['pasien_nama'],
                'pasien_no_rm' => $data['pasien_no_rm'] ?? null,
                'is_active' => true,
            ]);

            $today = \Carbon\Carbon::today();
            $dayOfWeek = (int) $today->format('N');
            $currentTime = \Carbon\Carbon::now()->format('H:i');
            
            if (in_array($dayOfWeek, $recurringData['recurring_hari']) && substr($data['jam'], 0, 5) > $currentTime) {
                \App\Models\TransportRequest::create([
                    'user_id' => $template->user_id,
                    'jenis' => $template->jenis,
                    'nomor_pengajuan' => \App\Models\TransportRequest::generateNomor(),
                    'tanggal' => $today->format('Y-m-d'),
                    'jam' => substr($template->jam, 0, 5),
                    'tanggal_sampai' => $today->format('Y-m-d'),
                    'jam_sampai' => $template->jam_sampai ? substr($template->jam_sampai, 0, 5) : null,
                    'keperluan' => $template->keperluan,
                    'prioritas' => $template->prioritas,
                    'pemohon_nama' => $template->pemohon_nama,
                    'pemohon_unit' => $template->pemohon_unit,
                    'jumlah_penumpang' => $template->jumlah_penumpang,
                    'alamat_asal' => $template->alamat_asal,
                    'alamat_tujuan' => $template->alamat_tujuan,
                    'keterangan' => $template->keterangan,
                    'pasien_nama' => $template->pasien_nama,
                    'pasien_no_rm' => $template->pasien_no_rm,
                    'kontak' => $request->user()->phone ?? '',
                    'signature_pemohon' => \Illuminate\Support\Str::random(32),
                    'signature_pemohon_at' => now(),
                ]);
            }

            return redirect()->route('pengajuan.index')
                ->with('recurring_success', 'Template pengajuan berulang berhasil dibuat dan akan dijalankan secara otomatis.');
        }

        // Cegah double submission: cek apakah ada pengajuan identik dalam 10 detik terakhir
        $duplicate = TransportRequest::where('user_id', $request->user()->id)
            ->where('jenis', 'ambulance')
            ->where('tanggal', $data['tanggal'])
            ->where('jam', $data['jam'])
            ->where('pasien_nama', $data['pasien_nama'])
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($duplicate) {
            return redirect()->back()->withInput()
                ->withErrors(['duplicate' => 'Pengajuan yang sama sudah dikirim. Silakan tunggu sebentar.']);
        }

        if ($sampaiSelesai) {
            $data['tanggal_sampai'] = null;
            $data['jam_sampai'] = null;
        }

        $data['kontak'] = '';

        if (!$sampaiSelesai) {
            $mulai = Carbon::parse($data['tanggal'].' '.$data['jam']);
            $sampai = Carbon::parse($data['tanggal_sampai'].' '.$data['jam_sampai']);
            if ($sampai->lte($mulai)) {
                $sampai->addDay();
                $data['tanggal_sampai'] = Carbon::parse($data['tanggal_sampai'])->addDay()->toDateString();
            }
        }

        $transportRequest = TransportRequest::create([
            'user_id' => $request->user()->id,
            'jenis' => 'ambulance',
            'nomor_pengajuan' => TransportRequest::generateNomor(),
            'unit_mobil' => null,
            'keperluan' => $purpose,
            'prioritas' => $data['prioritas'],
            ...collect($data)->except(['prioritas'])->all(),
            'alamat_asal' => $alamatAsal,
            'alamat_tujuan' => $alamatTujuan,
            'ruangan' => null,
            'pendamping_nama' => null,
            'kondisi_pasien' => null,
            // Auto-sign pemohon
            'signature_pemohon' => \Illuminate\Support\Str::random(32),
            'signature_pemohon_at' => now(),
        ]);

        return redirect()->route('pengajuan.success', $transportRequest)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function checkAmbulanceAvailability(Request $request)
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam' => ['required', 'date_format:H:i'],
            'tanggal_sampai' => ['required', 'date'],
            'jam_sampai' => ['required', 'date_format:H:i'],
        ]);

        // User prioritas tinggi selalu tersedia
        if (auth()->user()->isPriority()) {
            $totalUnits = \App\Models\Vehicle::where('type', 'ambulance')->where('is_active', true)->count();
            return response()->json([
                'available' => true,
                'total_units' => $totalUnits,
                'used_units' => 0,
                'available_units' => $totalUnits,
            ]);
        }

        // Jika sampai_selesai, gunakan akhir hari
        if (empty($data['tanggal_sampai']) || empty($data['jam_sampai'])) {
            $data['tanggal_sampai'] = $data['tanggal'];
            $data['jam_sampai'] = '23:59';
        }

        $mulai = Carbon::parse($data['tanggal'].' '.$data['jam']);
        $sampai = Carbon::parse($data['tanggal_sampai'].' '.$data['jam_sampai']);
        if ($sampai->lte($mulai)) $sampai->addDay();

        $totalUnits = \App\Models\Vehicle::where('type', 'ambulance')->where('is_active', true)->count();

        // Hitung pengajuan yang overlap pada rentang waktu ini dengan status disetujui atau sedang digunakan
        $conflicting = TransportRequest::where('jenis', 'ambulance')
            ->whereIn('status', ['diproses', 'digunakan'])
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                // null = sampai selesai (seharian penuh hingga 23:59)
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : Carbon::parse($r->tanggal->format('Y-m-d').' 23:59');
                if ($rSampai->lte($rMulai)) $rSampai->addDay();
                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            });

        // Hitung unit unik yang sudah di-assign; sisanya dihitung per pengajuan tanpa unit
        $assignedUnits = $conflicting->whereNotNull('unit_mobil')->pluck('unit_mobil')->unique()->count();
        $unassignedCount = $conflicting->whereNull('unit_mobil')->count();
        $usedUnits = $assignedUnits + $unassignedCount;

        $available = $usedUnits < $totalUnits;

        return response()->json([
            'available' => $available,
            'total_units' => $totalUnits,
            'used_units' => $usedUnits,
            'available_units' => max(0, $totalUnits - $usedUnits),
        ]);
    }

    public function print(Request $request, TransportRequest $transportRequest)
    {
        abort_unless($transportRequest->user_id === $request->user()->id, 403);
        $transportRequest->load('driver');
        return view('admin.transport.print', compact('transportRequest'));
    }

    public function success(Request $request, TransportRequest $transportRequest)
    {
        abort_unless($transportRequest->user_id === $request->user()->id, 403);

        return view('user.transport.success', [
            'item' => $transportRequest,
        ]);
    }

    public function profil()
    {
        $user = auth()->user();
        $totalPengajuan = $user->transportRequests()->count();
        $totalDisetujui = $user->transportRequests()->whereIn('status', ['diproses', 'digunakan', 'selesai'])->count();
        $totalDitolak   = $user->transportRequests()->where('status', 'ditolak')->count();

        return view('user.profil', compact('user', 'totalPengajuan', 'totalDisetujui', 'totalDitolak'));
    }
}

