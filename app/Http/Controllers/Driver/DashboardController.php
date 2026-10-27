<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\CancelTransportRequestRequest;
use App\Http\Requests\Driver\CompleteTransportRequestRequest;
use App\Models\TransportRequest;
use App\Services\TransportWorkflowService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly TransportWorkflowService $workflow) {}

    public function profil()
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return view('driver.no_driver');
        }

        $totalTugas = TransportRequest::where('driver_id', $driver->id)->count();
        $tugasSelesai = TransportRequest::where('driver_id', $driver->id)->where('status', 'selesai')->count();
        $tugasAktif = TransportRequest::where('driver_id', $driver->id)->where('status', 'digunakan')->count();

        return view('driver.profil', compact('driver', 'totalTugas', 'tugasSelesai', 'tugasAktif'));
    }

    public function history(Request $request)
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return view('driver.no_driver');
        }

        $query = TransportRequest::where('driver_id', $driver->id)
            ->whereIn('status', ['selesai', 'tidak_disetujui'])
            ->with('user')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $historyRequests = $query->paginate(10)->withQueryString();

        return view('driver.history', compact('driver', 'historyRequests'));
    }

    public function index()
    {
        $driver = auth()->user()->driver;

        if (! $driver) {
            return view('driver.no_driver');
        }

        $activeRequests = TransportRequest::where('driver_id', $driver->id)
            ->where('status', 'digunakan')
            ->with('user')
            ->orderByRaw("CONCAT(tanggal, ' ', jam) ASC")
            ->get();

        $totalTugas = TransportRequest::where('driver_id', $driver->id)->count();
        $tugasSaatIni = $activeRequests->count();
        $tugasSelesai = TransportRequest::where('driver_id', $driver->id)->where('status', 'selesai')->count();

        return view('driver.dashboard', compact('driver', 'activeRequests', 'totalTugas', 'tugasSaatIni', 'tugasSelesai'));
    }

    public function detail(TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (! $driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        return view('driver.detail', compact('transportRequest'));
    }

    public function print(TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (! $driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        $transportRequest->load('driver');

        return view('admin.transport.print', compact('transportRequest'));
    }

    public function cancel(CancelTransportRequestRequest $request, TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (! $driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        if ($transportRequest->status !== 'digunakan') {
            return back()->withErrors(['status' => 'Pengajuan tidak dalam status digunakan.']);
        }

        $this->workflow->cancelByDriver($transportRequest, $request->validated('rejection_reason'));

        return redirect()->route('driver.dashboard')
            ->with('success', 'Perjalanan berhasil dibatalkan.');
    }

    public function complete(CompleteTransportRequestRequest $request, TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (! $driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        if ($transportRequest->status !== 'digunakan') {
            return back()->withErrors(['status' => 'Pengajuan tidak dalam status digunakan.']);
        }

        $this->workflow->completeByDriver($transportRequest, $request->validated());

        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengajuan berhasil diselesaikan.');
    }
}
