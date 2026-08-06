<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransportRequestController extends Controller
{
    /**
     * Cek apakah masih ada unit tersedia untuk jenis dan rentang waktu pengajuan ini.
     * Menghitung pengajuan berstatus diproses + digunakan yang overlap (exclude pengajuan ini sendiri).
     */
    private function isUnitAvailable(TransportRequest $transportRequest): bool
    {
        $jenis = $transportRequest->jenis;
        $mulai = \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' '.$transportRequest->jam);
        $sampai = ($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
            ? \Carbon\Carbon::parse($transportRequest->tanggal_sampai->format('Y-m-d').' '.$transportRequest->jam_sampai)
            : \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' 23:59');
        if ($sampai->lte($mulai)) $sampai->addDay();

        $totalUnits = \App\Models\Vehicle::where('type', $jenis)->where('is_active', true)->count();

        $conflicting = TransportRequest::where('jenis', $jenis)
            ->whereIn('status', ['diproses', 'digunakan'])
            ->where('id', '!=', $transportRequest->id)
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = \Carbon\Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? \Carbon\Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : $rMulai->copy()->addHour();
                if ($rSampai->lte($rMulai)) $rSampai->addDay();
                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            });

        $assignedUnits = $conflicting->whereNotNull('unit_mobil')->pluck('unit_mobil')->unique()->count();
        $unassignedCount = $conflicting->whereNull('unit_mobil')->count();
        $usedUnits = $assignedUnits + $unassignedCount;

        return $usedUnits < $totalUnits;
    }
    public function dashboard()
    {
        $counts = TransportRequest::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'total' => TransportRequest::count(),
            'diajukan' => $counts['diajukan'] ?? 0,
            'diproses' => $counts['diproses'] ?? 0,
            'digunakan' => $counts['digunakan'] ?? 0,
            'selesai' => $counts['selesai'] ?? 0,
            'tidak_disetujui' => $counts['tidak_disetujui'] ?? 0,
        ];

        // Verify total matches sum of all statuses (for data integrity)
        $calculatedTotal = $summary['diajukan'] + $summary['diproses'] + $summary['digunakan'] + $summary['selesai'] + $summary['tidak_disetujui'];
        if ($summary['total'] !== $calculatedTotal) {
            // Log discrepancy for debugging
            \Log::warning('Dashboard total count mismatch', [
                'total_from_count' => $summary['total'],
                'calculated_total' => $calculatedTotal,
                'status_counts' => $counts->toArray()
            ]);
        }

        // Beberapa data terbaru untuk konteks cepat di dashboard
        $latest = TransportRequest::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Kendaraan yang sedang digunakan
        $activeVehicles = TransportRequest::with('user')
            ->where('status', 'digunakan')
            ->orderByRaw("CONCAT(tanggal, ' ', jam) ASC")
            ->get();

        // Pengajuan disetujui (diproses) yang akan digunakan hari ini
        $approvedToday = TransportRequest::where('status', 'diproses')
            ->whereDate('tanggal', today())
            ->count();

        return view('admin.dashboard', compact('summary', 'latest', 'activeVehicles', 'approvedToday'));
    }

    public function index(Request $request)
    {
        $query = TransportRequest::with('user');

        // Tentukan sorting berdasarkan status
        $selectedStatus = $request->input('status');
        if ($request->has('status') && $request->filled('status')) {
            // Status tertentu dipilih
            $fifoStatuses = ['diajukan', 'diproses', 'digunakan']; // Status yang menggunakan FIFO berdasarkan waktu pengajuan
            if (in_array($selectedStatus, $fifoStatuses)) {
                // Untuk status menunggu, disetujui, dan digunakan: prioritas tinggi dulu, lalu FIFO
                $query->leftJoin('users', 'transport_requests.user_id', '=', 'users.id')
                      ->orderByRaw("COALESCE(users.priority_level, 0) DESC")
                      ->orderByRaw("CONCAT(transport_requests.tanggal, ' ', transport_requests.jam) ASC")
                      ->select('transport_requests.*');
            } else {
                // Untuk status lain (selesai, tidak_disetujui, dll): urutkan dari yang terbaru dibuat
                $query->orderBy('created_at', 'desc');
            }
            $query->where('status', $selectedStatus);
        } elseif ($request->has('status') && !$request->filled('status')) {
            // "Semua Status" dipilih: urutkan dari yang terbaru dibuat
            $query->orderBy('created_at', 'desc');
        } else {
            // Tidak ada parameter status (first load): default ke 'diajukan' dengan prioritas tinggi dulu, lalu FIFO
            $query->leftJoin('users', 'transport_requests.user_id', '=', 'users.id')
                  ->where('transport_requests.status', 'diajukan')
                  ->orderByRaw("COALESCE(users.priority_level, 0) DESC")
                  ->orderByRaw("CONCAT(transport_requests.tanggal, ' ', transport_requests.jam) ASC")
                  ->select('transport_requests.*');
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.transport.index', compact('items'));
    }

    private function applyLaporanFilters($query, Request $request): void
    {
        $like = fn($val) => '%' . strtolower($val) . '%';

        if ($request->filled('nomor'))       $query->whereRaw('LOWER(nomor_pengajuan) LIKE ?', [$like($request->nomor)]);
        if ($request->filled('jenis'))       $query->where('jenis', $request->jenis);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('prioritas'))   $query->where('prioritas', $request->prioritas);
        if ($request->filled('nip_pemohon')) $query->whereHas('user', fn($q) => $q->whereRaw('LOWER(nip) LIKE ?', [$like($request->nip_pemohon)]));
        if ($request->filled('pemohon'))     $query->whereHas('user', fn($q) => $q->whereRaw('LOWER(first_name) LIKE ?', [$like($request->pemohon)])->orWhereRaw('LOWER(last_name) LIKE ?', [$like($request->pemohon)]));
        if ($request->filled('unit_kerja'))  $query->whereHas('user', fn($q) => $q->whereRaw('LOWER(unit_kerja) LIKE ?', [$like($request->unit_kerja)]));
        if ($request->filled('keperluan'))   $query->whereRaw('LOWER(keperluan) LIKE ?', [$like($request->keperluan)]);
        if ($request->filled('tujuan'))      $query->whereRaw('LOWER(alamat_tujuan) LIKE ?', [$like($request->tujuan)]);
        if ($request->filled('supir'))       $query->whereHas('driver', fn($q) => $q->whereRaw('LOWER(name) LIKE ?', [$like($request->supir)]));
        if ($request->filled('unit_mobil'))  $query->whereRaw('LOWER(unit_mobil) LIKE ?', [$like($request->unit_mobil)]);
        if ($request->filled('plat_nomor'))  $query->whereRaw('LOWER(plat_nomor) LIKE ?', [$like($request->plat_nomor)]);
        if ($request->filled('tanggal_dari')) $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        if ($request->filled('tanggal_sampai_filter')) $query->whereDate('tanggal', '<=', $request->tanggal_sampai_filter);
    }

    public function laporan(Request $request)
    {
        $query = TransportRequest::with(['user', 'driver.user'])->latest();
        $this->applyLaporanFilters($query, $request);
        $items = $query->paginate(10)->withQueryString();
        return view('admin.laporan', compact('items'));
    }

    public function laporanPrint(Request $request)
    {
        $query = TransportRequest::with(['user', 'driver.user'])->latest();
        $this->applyLaporanFilters($query, $request);
        $items = $query->get();
        return view('admin.laporan-print', compact('items'));
    }

    public function laporanExport(Request $request)
    {
        $query = TransportRequest::with(['user', 'driver.user'])->latest();
        $this->applyLaporanFilters($query, $request);
        $items = $query->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengajuan');

        // === JUDUL ===
        $sheet->mergeCells('A1:X1');
        $sheet->setCellValue('A1', 'LAPORAN PENGAJUAN TRANSPORTASI');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Sub-judul filter info
        $filterInfo = 'Diekspor: ' . now()->format('d/m/Y H:i');
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai_filter')) {
            $filterInfo .= '  |  Periode: ' . ($request->tanggal_dari ?? '...') . ' s/d ' . ($request->tanggal_sampai_filter ?? '...');
        }
        if ($request->filled('status')) {
            $filterInfo .= '  |  Status: ' . ucfirst($request->status);
        }
        if ($request->filled('jenis')) {
            $filterInfo .= '  |  Jenis: ' . ucfirst($request->jenis);
        }
        $sheet->mergeCells('A2:X2');
        $sheet->setCellValue('A2', $filterInfo);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '666666']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(14);

        // Baris kosong
        $sheet->getRowDimension(3)->setRowHeight(6);

        // === HEADER ===
        $headers = [
            'A' => ['No. Pengajuan', 20],
            'B' => ['Tgl Dibuat',    13],
            'C' => ['Jam Dibuat',    10],
            'D' => ['NIP Pemohon',   15],
            'E' => ['Nama Pemohon',  22],
            'F' => ['Jabatan',       18],
            'G' => ['Profesi',       18],
            'H' => ['Unit Kerja',    20],
            'I' => ['Jenis',          9],
            'J' => ['Prioritas',     12],
            'K' => ['Keperluan',     25],
            'L' => ['Tgl Berangkat', 14],
            'M' => ['Jam Berangkat', 13],
            'N' => ['Jam Sampai',    11],
            'O' => ['Tujuan',        25],
            'P' => ['Unit Kendaraan',16],
            'Q' => ['Plat Nomor',    12],
            'R' => ['NIP Supir',     15],
            'S' => ['Nama Supir',    20],
            'T' => ['KM Awal',       10],
            'U' => ['KM Akhir',      10],
            'V' => ['Jarak (km)',    11],
            'W' => ['Tgl Kembali',   13],
            'X' => ['Jam Tiba',      10],
            'Y' => ['Status',        14],
        ];

        $headerRow = 4;
        foreach ($headers as $col => [$label, $width]) {
            $sheet->setCellValue($col . $headerRow, $label);
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $headerRange = 'A' . $headerRow . ':Y' . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '007774']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // Freeze header
        $sheet->freezePane('A' . ($headerRow + 1));

        // === DATA ===
        $row = $headerRow + 1;
        foreach ($items as $i => $item) {
            $statusLabel = match($item->status) {
                'diproses'        => 'Disetujui',
                'tidak_disetujui' => 'Tidak Disetujui',
                default           => ucfirst($item->status),
            };
            $nipSupir  = $item->driver && $item->driver->user ? $item->driver->user->nip : '';
            $jarak     = ($item->km_awal && $item->km_akhir) ? ($item->km_akhir - $item->km_awal) : null;
            $tglKembali = $item->tanggal_sampai
                ? $item->tanggal_sampai->format('d/m/Y')
                : ($item->status === 'selesai' ? $item->updated_at->format('d/m/Y') : '');

            $rowBg = ($i % 2 === 0) ? 'FFFFFF' : 'F0FAFA';

            $sheet->fromArray([
                $item->nomor_pengajuan,
                $item->created_at->format('d/m/Y'),
                $item->created_at->format('H:i'),
                $item->user->nip ?? '',
                $item->user->full_name ?? $item->pemohon_nama ?? '',
                $item->user->jabatan ?? '',
                $item->user->profesi ?? '',
                $item->user->unit_kerja ?? $item->pemohon_unit ?? '',
                ucfirst($item->jenis),
                $item->prioritas === 'segera' ? 'Segera / CITO' : 'Biasa',
                $item->keperluan ?? '',
                $item->tanggal->format('d/m/Y'),
                substr($item->jam, 0, 5),
                $item->jam_sampai ? substr($item->jam_sampai, 0, 5) : '',
                $item->alamat_tujuan ?? '',
                $item->unit_mobil ? ucwords(str_replace('_', ' ', $item->unit_mobil)) : '',
                $item->plat_nomor ?? '',
                $nipSupir,
                $item->driver->name ?? '',
                $item->km_awal ?? '',
                $item->km_akhir ?? '',
                $jarak,
                $tglKembali,
                $item->jam_kedatangan ?? '',
                $statusLabel,
            ], null, 'A' . $row);

            // Zebra stripe
            $sheet->getStyle('A'.$row.':Y'.$row)->applyFromArray([
                'fill'    => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'font'    => ['size' => 9],
            ]);

            // Alignment per kolom
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // No. Pengajuan
            $sheet->getStyle('B'.$row.':C'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$row)->getFont()->setName('Courier New');  // NIP mono
            $sheet->getStyle('I'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L'.$row.':N'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q'.$row)->getFont()->setName('Courier New');  // Plat mono
            $sheet->getStyle('R'.$row)->getFont()->setName('Courier New');  // NIP supir mono
            $sheet->getStyle('T'.$row.':V'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('W'.$row.':X'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Y'.$row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        // Baris total
        if ($items->count() > 0) {
            $sheet->mergeCells('A'.$row.':S'.$row);
            $sheet->setCellValue('A'.$row, 'Total: ' . $items->count() . ' data');
            $sheet->getStyle('A'.$row)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5F5']],
            ]);
        }

        // Auto filter
        $sheet->setAutoFilter('A'.$headerRow.':Y'.$headerRow);

        $filename = 'laporan-pengajuan-' . now()->format('Ymd-His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function laporanDetail(TransportRequest $transportRequest)
    {
        $transportRequest->load(['user', 'driver.user']);
        return view('admin.laporan-detail', compact('transportRequest'));
    }

    public function show(TransportRequest $transportRequest)
    {
        $vehicleType = $transportRequest->jenis === 'ambulance' ? 'ambulance' : 'umum';

        $mulai = \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' '.$transportRequest->jam);
        $sampai = ($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
            ? \Carbon\Carbon::parse($transportRequest->tanggal_sampai->format('Y-m-d').' '.$transportRequest->jam_sampai)
            : \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' 23:59');
        if ($sampai->lte($mulai)) $sampai->addDay();

        // Supir yang sedang bertugas pada rentang waktu ini
        $busyDriverIds = TransportRequest::where('status', 'digunakan')
            ->whereNotNull('driver_id')
            ->where('id', '!=', $transportRequest->id)
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = \Carbon\Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? \Carbon\Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : $rMulai->copy()->addHour();
                if ($rSampai->lte($rMulai)) $rSampai->addDay();
                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            })
            ->pluck('driver_id')
            ->unique()
            ->values();

        $drivers = \App\Models\Driver::where('is_active', true)
            ->whereNotIn('id', $busyDriverIds)
            ->orderBy('name')
            ->get();

        // Unit kendaraan yang sedang digunakan pada rentang waktu ini
        $busyVehicleNames = TransportRequest::where('status', 'digunakan')
            ->whereNotNull('unit_mobil')
            ->where('id', '!=', $transportRequest->id)
            ->get()
            ->filter(function ($r) use ($mulai, $sampai) {
                $rMulai = \Carbon\Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                    ? \Carbon\Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                    : $rMulai->copy()->addHour();
                if ($rSampai->lte($rMulai)) $rSampai->addDay();
                return $mulai->lt($rSampai) && $rMulai->lt($sampai);
            })
            ->pluck('unit_mobil')
            ->unique()
            ->values();

        $vehicles = \App\Models\Vehicle::where('type', $vehicleType)
            ->where('is_active', true)
            ->whereNotIn('name', $busyVehicleNames)
            ->orderBy('name')
            ->get();

        $unitAvailable = $transportRequest->status === 'diajukan'
            ? $this->isUnitAvailable($transportRequest)
            : true;

        return view('admin.transport.show', compact('transportRequest', 'drivers', 'vehicles', 'unitAvailable'));
    }

    public function update(Request $request, TransportRequest $transportRequest)
    {
        $currentStatus = $transportRequest->status;
        $newStatus = $request->input('status');
        
        if ($currentStatus === 'diajukan' && $newStatus === 'diproses') {
            $isPriorityUser = $transportRequest->user && $transportRequest->user->isPriority();

            // Cek ketersediaan unit — skip untuk user prioritas
            if (!$isPriorityUser && !$this->isUnitAvailable($transportRequest)) {
                return redirect()->back()->withErrors(['status' => 'Tidak dapat menyetujui — semua unit kendaraan sudah penuh di waktu tersebut.']);
            }

            $data = $request->validate([
                'status' => ['required', 'in:diproses,tidak_disetujui'],
            ]);
            // Auto-sign pengelola_1
            $data['signature_pengelola_1'] = \Illuminate\Support\Str::random(32);
            $data['signature_pengelola_1_at'] = now();
            $data['signature_pengelola_1_name'] = auth()->user()->full_name;

            // Jika user prioritas dan unit penuh, batalkan pengajuan user biasa terbaru yang overlap
            if ($isPriorityUser && !$this->isUnitAvailable($transportRequest)) {
                $mulai = \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' '.$transportRequest->jam);
                $sampai = ($transportRequest->tanggal_sampai && $transportRequest->jam_sampai)
                    ? \Carbon\Carbon::parse($transportRequest->tanggal_sampai->format('Y-m-d').' '.$transportRequest->jam_sampai)
                    : \Carbon\Carbon::parse($transportRequest->tanggal->format('Y-m-d').' 23:59');
                if ($sampai->lte($mulai)) $sampai->addDay();

                $overlapping = TransportRequest::where('jenis', $transportRequest->jenis)
                    ->where('status', 'diproses')
                    ->where('id', '!=', $transportRequest->id)
                    ->whereHas('user', fn($q) => $q->where('priority_level', 0))
                    ->get()
                    ->filter(function ($r) use ($mulai, $sampai) {
                        $rMulai = \Carbon\Carbon::parse($r->tanggal->format('Y-m-d').' '.$r->jam);
                        $rSampai = ($r->tanggal_sampai && $r->jam_sampai)
                            ? \Carbon\Carbon::parse($r->tanggal_sampai->format('Y-m-d').' '.$r->jam_sampai)
                            : \Carbon\Carbon::parse($r->tanggal->format('Y-m-d').' 23:59');
                        if ($rSampai->lte($rMulai)) $rSampai->addDay();
                        return $mulai->lt($rSampai) && $rMulai->lt($sampai);
                    })
                    ->sortByDesc('created_at');

                $toBump = $overlapping->first();
                if ($toBump) {
                    $toBump->update([
                        'status' => 'tidak_disetujui',
                        'rejection_reason' => 'Dialihkan karena pengajuan dari pengguna prioritas.',
                    ]);
                }
            }

        } elseif ($currentStatus === 'digunakan' && $newStatus === 'digunakan' && $request->input('_edit_digunakan')) {
            // Edit data saat status digunakan
            $data = $request->validate([
                'status'     => ['required', 'in:digunakan'],
                'unit_mobil' => ['nullable', 'string', 'max:100'],
                'plat_nomor' => ['nullable', 'string', 'max:20'],
                'driver_id'  => ['nullable', 'exists:drivers,id'],
                'km_awal'    => ['nullable', 'integer', 'min:0'],
            ]);

            if (empty($data['plat_nomor']) && !empty($data['unit_mobil'])) {
                $vehicle = \App\Models\Vehicle::where('name', $data['unit_mobil'])->first();
                if ($vehicle) $data['plat_nomor'] = $vehicle->plate_number;
            }

            unset($data['status']);
            $transportRequest->update($data);

            return redirect()->route('admin.transport.show', $transportRequest)
                ->with('success', 'Data kendaraan berhasil diperbarui.');

        } elseif ($currentStatus === 'diproses' && $newStatus === 'digunakan') {
            $data = $request->validate([
                'status' => ['required', 'in:digunakan'],
                'unit_mobil' => ['required', 'string', 'max:100'],
                'plat_nomor' => ['nullable', 'string', 'max:20'],
                'driver_id' => ['required', 'exists:drivers,id'],
                'km_awal' => ['required', 'integer', 'min:0'],
            ]);

            // Validasi km_awal tidak boleh kurang dari last_km kendaraan
            $vehicle = \App\Models\Vehicle::where('name', $data['unit_mobil'])->first();
            if ($vehicle && $vehicle->last_km !== null && $data['km_awal'] < $vehicle->last_km) {
                throw ValidationException::withMessages([
                    'km_awal' => 'KM berangkat tidak boleh kurang dari ' . number_format($vehicle->last_km) . ' km (KM terakhir kendaraan ini).',
                ]);
            }

            if (empty($data['plat_nomor']) && !empty($data['unit_mobil'])) {
                if ($vehicle) {
                    $data['plat_nomor'] = $vehicle->plate_number;
                }
            }
            // Auto-sign driver
            $data['signature_driver'] = \Illuminate\Support\Str::random(32);
            $data['signature_driver_at'] = now();

        } elseif ($currentStatus === 'digunakan' && $newStatus === 'selesai') {
            $data = $request->validate([
                'status' => ['required', 'in:selesai'],
                'km_akhir' => ['required', 'integer', 'min:0'],
                'jam_kedatangan' => ['required', 'string', 'max:10', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'],
            ]);
            
            if ($data['km_akhir'] <= $transportRequest->km_awal) {
                throw ValidationException::withMessages(['km_akhir' => 'KM tiba harus lebih besar dari KM keberangkatan.']);
            }
            // Auto-sign pengelola_2
            $data['signature_pengelola_2'] = \Illuminate\Support\Str::random(32);
            $data['signature_pengelola_2_at'] = now();
            $data['signature_pengelola_2_name'] = auth()->user()->full_name;

        } elseif ($currentStatus === 'diajukan' && $newStatus === 'tidak_disetujui') {
            $data = $request->validate([
                'status' => ['required', 'in:tidak_disetujui'],
                'rejection_reason' => ['required', 'string', 'max:500'],
            ]);
        } elseif ($currentStatus === 'digunakan' && $newStatus === 'tidak_disetujui') {
            $data = $request->validate([
                'status' => ['required', 'in:tidak_disetujui'],
                'rejection_reason' => ['required', 'string', 'max:500'],
            ]);
        } else {
            return redirect()->back()->withErrors(['status' => 'Transisi status tidak valid.']);
        }

        $transportRequest->update($data);

        // Jika selesai, update last_km di tabel kendaraan
        if ($newStatus === 'selesai' && $transportRequest->unit_mobil) {
            \App\Models\Vehicle::where('name', $transportRequest->unit_mobil)
                ->update(['last_km' => $data['km_akhir']]);
        }

        return redirect()->route('admin.transport.show', $transportRequest)
            ->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function print(TransportRequest $transportRequest)
    {
        $transportRequest->load('driver');
        
        // Jika plat_nomor kosong tapi unit_mobil ada, coba ambil dari master vehicle
        if (empty($transportRequest->plat_nomor) && !empty($transportRequest->unit_mobil)) {
            $vehicle = \App\Models\Vehicle::where('name', $transportRequest->unit_mobil)->first();
            if ($vehicle) {
                $transportRequest->plat_nomor = $vehicle->plate_number;
            }
        }
        
        return view('admin.transport.print', compact('transportRequest'));
    }
}
