<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\TransportRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function profil()
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return view('driver.no_driver');
        }

        $totalTugas   = TransportRequest::where('driver_id', $driver->id)->count();
        $tugasSelesai = TransportRequest::where('driver_id', $driver->id)->where('status', 'selesai')->count();
        $tugasAktif   = TransportRequest::where('driver_id', $driver->id)->where('status', 'digunakan')->count();

        return view('driver.profil', compact('driver', 'totalTugas', 'tugasSelesai', 'tugasAktif'));
    }

    public function history(Request $request)
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return view('driver.no_driver');
        }

        $query = TransportRequest::where('driver_id', $driver->id)
            ->whereIn('status', ['selesai', 'tidak_disetujui'])
            ->with('user')
            ->latest();

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('jenis'))   $query->where('jenis', $request->jenis);
        if ($request->filled('tanggal')) $query->whereDate('tanggal', $request->tanggal);

        $historyRequests = $query->paginate(10)->withQueryString();

        return view('driver.history', compact('driver', 'historyRequests'));
    }

    public function index()
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return view('driver.no_driver');
        }

        $activeRequests = TransportRequest::where('driver_id', $driver->id)
            ->where('status', 'digunakan')
            ->with('user')
            ->orderByRaw("CONCAT(tanggal, ' ', jam) ASC")
            ->get();

        $totalTugas    = TransportRequest::where('driver_id', $driver->id)->count();
        $tugasSaatIni  = $activeRequests->count();
        $tugasSelesai  = TransportRequest::where('driver_id', $driver->id)->where('status', 'selesai')->count();

        return view('driver.dashboard', compact('driver', 'activeRequests', 'totalTugas', 'tugasSaatIni', 'tugasSelesai'));
    }

    public function detail(TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (!$driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        return view('driver.detail', compact('transportRequest'));
    }

    public function print(TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (!$driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        $transportRequest->load('driver');
        return view('admin.transport.print', compact('transportRequest'));
    }

    public function cancel(Request $request, TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (!$driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        if ($transportRequest->status !== 'digunakan') {
            return back()->withErrors(['status' => 'Pengajuan tidak dalam status digunakan.']);
        }

        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $transportRequest->update([
            'status' => 'tidak_disetujui',
            'rejection_reason' => $data['rejection_reason'],
        ]);

        return redirect()->route('driver.dashboard')
            ->with('success', 'Perjalanan berhasil dibatalkan.');
    }

    public function complete(Request $request, TransportRequest $transportRequest)
    {
        $driver = auth()->user()->driver;

        if (!$driver || $transportRequest->driver_id !== $driver->id) {
            abort(403);
        }

        if ($transportRequest->status !== 'digunakan') {
            return back()->withErrors(['status' => 'Pengajuan tidak dalam status digunakan.']);
        }

        $data = $request->validate([
            'km_akhir' => ['required', 'integer', 'min:0'],
            'jam_kedatangan' => ['required', 'string', 'max:10', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9]$/'],
            'biaya_tol' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($data['km_akhir'] <= $transportRequest->km_awal) {
            throw ValidationException::withMessages(['km_akhir' => 'KM tiba harus lebih besar dari KM keberangkatan.']);
        }

        // Auto-sign driver + pengelola_2 saat selesai
        $transportRequest->update(array_merge($data, [
            'status' => 'selesai',
            'signature_driver' => $transportRequest->signature_driver ?: \Illuminate\Support\Str::random(32),
            'signature_driver_at' => $transportRequest->signature_driver_at ?: now(),
            'signature_pengelola_2' => \Illuminate\Support\Str::random(32),
            'signature_pengelola_2_at' => now(),
            'signature_pengelola_2_name' => $transportRequest->signature_pengelola_1_name,
        ]));

        // Simpan km_akhir ke last_km kendaraan
        if ($transportRequest->unit_mobil) {
            \App\Models\Vehicle::where('name', $transportRequest->unit_mobil)
                ->update(['last_km' => $data['km_akhir']]);
        }

        return redirect()->route('driver.dashboard')
            ->with('success', 'Pengajuan berhasil diselesaikan.');
    }
}
