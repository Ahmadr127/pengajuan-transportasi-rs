<?php

namespace App\Http\Controllers\Api;

use App\Models\TransportRequest;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasPermission('dashboard.admin.view')) {
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

            $latest = TransportRequest::with('user')->latest()->limit(5)->get();
            $activeVehicles = TransportRequest::with('user')->where('status', 'digunakan')->orderByRaw("CONCAT(tanggal, ' ', jam) ASC")->get();
            $approvedToday = TransportRequest::where('status', 'diproses')->whereDate('tanggal', today())->count();

            return $this->success([
                'summary' => $summary,
                'latest' => \App\Http\Resources\TransportRequestResource::collection($latest),
                'active_vehicles' => \App\Http\Resources\TransportRequestResource::collection($activeVehicles),
                'approved_today' => $approvedToday,
            ]);
        }

        // user stats
        $myTotal = TransportRequest::where('user_id', $user->id)->count();
        $myPending = TransportRequest::where('user_id', $user->id)->where('status', 'diajukan')->count();
        $myActive = TransportRequest::where('user_id', $user->id)->where('status', 'digunakan')->count();
        $myDone = TransportRequest::where('user_id', $user->id)->where('status', 'selesai')->count();

        return $this->success([
            'my_total' => $myTotal,
            'my_pending' => $myPending,
            'my_active' => $myActive,
            'my_done' => $myDone,
        ]);
    }
}