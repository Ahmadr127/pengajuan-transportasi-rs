<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transport\CheckAvailabilityRequest;
use App\Http\Requests\Transport\StoreAmbulanceRequest;
use App\Http\Requests\Transport\StoreUmumRequest;
use App\Models\TransportRequest;
use App\Services\AvailabilityService;
use App\Services\TransportRequestService;
use Illuminate\Http\Request;

class TransportRequestController extends Controller
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly TransportRequestService $requests,
    ) {}

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

    public function storeUmum(StoreUmumRequest $request)
    {
        $user = $request->user();

        if ($request->boolean('is_recurring')) {
            $this->requests->createRecurringUmum($user, $request->validated(), $request->input('recurring_hari', []));

            return redirect()->route('pengajuan.index')
                ->with('recurring_success', 'Template pengajuan berulang berhasil dibuat dan akan dijalankan secara otomatis.');
        }

        $transportRequest = $this->requests->createUmum(
            $user,
            $request->validated(),
            $request->boolean('sampai_selesai'),
        );

        return redirect()->route('pengajuan.success', $transportRequest)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function checkAvailability(CheckAvailabilityRequest $request)
    {
        return $this->availabilityResponse('umum', $request->validated());
    }

    public function createAmbulance()
    {
        $purpose = old('purpose', 'antar');

        return view('user.transport.ambulance_form', compact('purpose'));
    }

    public function storeAmbulance(StoreAmbulanceRequest $request)
    {
        $user = $request->user();

        if ($request->boolean('is_recurring')) {
            $this->requests->createRecurringAmbulance($user, $request->validated(), $request->input('recurring_hari', []));

            return redirect()->route('pengajuan.index')
                ->with('recurring_success', 'Template pengajuan berulang berhasil dibuat dan akan dijalankan secara otomatis.');
        }

        $transportRequest = $this->requests->createAmbulance(
            $user,
            $request->validated(),
            $request->boolean('sampai_selesai'),
        );

        return redirect()->route('pengajuan.success', $transportRequest)
            ->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function checkAmbulanceAvailability(CheckAvailabilityRequest $request)
    {
        return $this->availabilityResponse('ambulance', $request->validated());
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
        $totalDitolak = $user->transportRequests()->where('status', 'ditolak')->count();

        return view('user.profil', compact('user', 'totalPengajuan', 'totalDisetujui', 'totalDitolak'));
    }

    private function availabilityResponse(string $jenis, array $data)
    {
        [$mulai, $sampai] = $this->availability->windowFrom(
            $data['tanggal'],
            $data['jam'],
            $data['tanggal_sampai'] ?? null,
            $data['jam_sampai'] ?? null,
        );

        return response()->json(
            $this->availability->check(
                $jenis,
                $mulai,
                $sampai,
                null,
                AvailabilityService::NULL_END_OF_DAY,
                auth()->user()->isPriority(),
            )
        );
    }
}
