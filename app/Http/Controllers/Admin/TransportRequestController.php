<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTransportStatusRequest;
use App\Models\Driver;
use App\Models\TransportRequest;
use App\Models\Vehicle;
use App\Services\AvailabilityService;
use App\Services\LaporanExportService;
use App\Services\TransportRequestQueryService;
use App\Services\TransportWorkflowService;
use Illuminate\Http\Request;

class TransportRequestController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly TransportRequestQueryService $query,
        private readonly TransportWorkflowService $workflow,
        private readonly LaporanExportService $export,
    ) {}

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
                'status_counts' => $counts->toArray(),
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
        $items = $this->query->adminIndex($request->all());

        return view('admin.transport.index', compact('items'));
    }

    public function laporan(Request $request)
    {
        $items = $this->query->laporanQuery($request->all())->paginate(10)->withQueryString();

        return view('admin.laporan', compact('items'));
    }

    public function laporanPrint(Request $request)
    {
        $items = $this->query->laporanQuery($request->all())->get();

        return view('admin.laporan-print', compact('items'));
    }

    public function laporanExport(Request $request)
    {
        return $this->export->stream($request->all());
    }

    public function laporanDetail(TransportRequest $transportRequest)
    {
        $transportRequest->load(['user', 'driver.user']);

        return view('admin.laporan-detail', compact('transportRequest'));
    }

    public function show(TransportRequest $transportRequest)
    {
        $vehicleType = $transportRequest->jenis === 'ambulance' ? 'ambulance' : 'umum';

        [$mulai, $sampai] = $this->availability->targetWindow($transportRequest);

        // Supir yang sedang bertugas pada rentang waktu ini
        $busyDriverIds = $this->availability->busyDriverIds($mulai, $sampai, $transportRequest->id);

        $drivers = Driver::where('is_active', true)
            ->whereNotIn('id', $busyDriverIds)
            ->orderBy('name')
            ->get();

        // Unit kendaraan yang sedang digunakan pada rentang waktu ini
        $busyVehicleNames = $this->availability->busyVehicleNames($mulai, $sampai, $transportRequest->id);

        $vehicles = Vehicle::where('type', $vehicleType)
            ->where('is_active', true)
            ->whereNotIn('name', $busyVehicleNames)
            ->orderBy('name')
            ->get();

        $unitAvailable = $transportRequest->status === 'diajukan'
            ? $this->availability->isAvailable($transportRequest->jenis, $transportRequest)
            : true;

        return view('admin.transport.show', compact('transportRequest', 'drivers', 'vehicles', 'unitAvailable'));
    }

    public function update(UpdateTransportStatusRequest $request, TransportRequest $transportRequest)
    {
        $message = $this->workflow->updateByAdmin(
            $transportRequest,
            $request->validated(),
            $request->user(),
            $request->boolean('_edit_digunakan'),
        );

        return redirect()->route('admin.transport.show', $transportRequest)
            ->with('success', $message);
    }

    public function print(TransportRequest $transportRequest)
    {
        $transportRequest->load('driver');

        // Jika plat_nomor kosong tapi unit_mobil ada, coba ambil dari master vehicle
        if (empty($transportRequest->plat_nomor) && ! empty($transportRequest->unit_mobil)) {
            $vehicle = Vehicle::where('name', $transportRequest->unit_mobil)->first();
            if ($vehicle) {
                $transportRequest->plat_nomor = $vehicle->plate_number;
            }
        }

        return view('admin.transport.print', compact('transportRequest'));
    }
}
