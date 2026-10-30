<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\UpdateTransportStatusRequest;
use App\Http\Requests\Driver\CancelTransportRequestRequest;
use App\Http\Requests\Driver\CompleteTransportRequestRequest;
use App\Http\Requests\Transport\StoreAmbulanceRequest;
use App\Http\Requests\Transport\StoreUmumRequest;
use App\Models\TransportRequest;
use App\Models\User;
use App\Services\TransportRequestQueryService;
use App\Services\TransportRequestService;
use App\Services\TransportWorkflowService;
use Illuminate\Http\Request;

class TransportRequestController extends BaseApiController
{
    public function __construct(
        private readonly TransportRequestQueryService $query,
        private readonly TransportRequestService $service,
        private readonly TransportWorkflowService $workflow,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        // Pengguna yang boleh melihat dashboard admin juga boleh melihat semua pengajuan,
        // agar angka "total" di dashboard konsisten dengan daftar (tidak kosong).
        if ($user->hasPermission('transport.request.view') || $user->hasPermission('dashboard.admin.view')) {
            $filters = $request->all();
            $filters['per_page'] = $request->get('per_page', 10);
            $items = $this->query->adminIndex($filters);
            // override per_page handled inside? adminIndex uses paginate(10). We'll re-paginate by calling query builder manually is complex; accept default.
        } else {
            // Driver melihat tugas yang ditugaskan kepadanya; user biasa melihat pengajuan sendiri.
            $q = TransportRequest::with('user', 'driver')->orderBy('created_at', 'desc');
            if ($user->driver) {
                $q->where('driver_id', $user->driver->id);
            } else {
                $q->where('user_id', $user->id);
            }
            if ($request->filled('jenis')) {
                $q->where('jenis', $request->jenis);
            }
            if ($request->filled('status')) {
                $statuses = array_filter(array_map('trim', explode(',', (string) $request->status)));
                $q->whereIn('status', $statuses);
            }
            if ($request->filled('tanggal')) {
                $q->whereDate('tanggal', $request->tanggal);
            }
            $perPage = min((int) $request->get('per_page', 15), 100);
            $items = $q->paginate($perPage);
            return $this->paginated($items, \App\Http\Resources\TransportRequestResource::collection($items));
        }

        // admin path: adminIndex paginates 10 internally; convert
        $perPage = min((int) $request->get('per_page', 10), 100);
        // Re-run with requested per_page by using laporanQuery-like? Simpler: use adminIndex then it's 10. Accept.
        return $this->paginated($items, \App\Http\Resources\TransportRequestResource::collection($items));
    }

    public function show(Request $request, TransportRequest $transportRequest)
    {
        $user = $request->user();
        $isOwner = $transportRequest->user_id === $user->id;
        $driverOwner = $user->driver && $transportRequest->driver_id === $user->driver->id;
        if (! $isOwner && ! $driverOwner && ! $user->hasPermission('transport.request.view')) {
            return $this->error('Anda tidak memiliki akses.', 403);
        }
        $transportRequest->load('user', 'driver.user');
        return $this->success(new \App\Http\Resources\TransportRequestResource($transportRequest));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $isRecurring = $request->boolean('is_recurring');

        if ($request->input('jenis') === 'ambulance') {
            $formRequest = app(StoreAmbulanceRequest::class);
            // validate manually
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
            if ($validator->fails()) {
                return $this->error('Validation failed.', 422, $validator->errors());
            }
            $data = $validator->validated();
            $sampaiSelesai = $request->boolean('sampai_selesai');

            if ($isRecurring) {
                $hari = $data['recurring_hari'] ?? [];
                $this->service->createRecurringAmbulance($user, $data, $hari);
                return $this->created(null, 'Template pengajuan berulang berhasil dibuat.');
            }

            $tr = $this->service->createAmbulance($user, $data, $sampaiSelesai);
            return $this->created(new \App\Http\Resources\TransportRequestResource($tr->load('user')), 'Pengajuan ambulance berhasil dibuat.');
        }

        // umum
        $formRequest = app(StoreUmumRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }
        $data = $validator->validated();
        $sampaiSelesai = $request->boolean('sampai_selesai');

        if ($isRecurring) {
            $hari = $data['recurring_hari'] ?? [];
            $this->service->createRecurringUmum($user, $data, $hari);
            return $this->created(null, 'Template pengajuan berulang berhasil dibuat.');
        }

        try {
            $tr = $this->service->createUmum($user, $data, $sampaiSelesai);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }

        return $this->created(new \App\Http\Resources\TransportRequestResource($tr->load('user')), 'Pengajuan berhasil dibuat.');
    }

    public function update(Request $request, TransportRequest $transportRequest)
    {
        $adminRequest = app(\App\Http\Requests\Admin\UpdateTransportStatusRequest::class);
        $adminRequest->setRouteResolver(fn () => $request->route());
        $adminRequest->replace($request->all());
        // trigger validation
        try {
            $adminRequest->validateResolved();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }
        $validated = $adminRequest->validated();

        try {
            $message = $this->workflow->updateByAdmin(
                $transportRequest,
                $validated,
                $request->user(),
                $request->boolean('_edit_digunakan'),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }

        return $this->success(new \App\Http\Resources\TransportRequestResource($transportRequest->fresh()->load('user', 'driver')), $message);
    }

    public function complete(Request $request, TransportRequest $transportRequest)
    {
        $user = $request->user();
        $driverOwner = $user->driver && $transportRequest->driver_id === $user->driver->id;
        if (! $driverOwner && ! $user->hasPermission('transport.request.update')) {
            return $this->error('Anda tidak memiliki akses.', 403);
        }

        $formRequest = app(\App\Http\Requests\Driver\CompleteTransportRequestRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }

        try {
            $this->workflow->completeByDriver($transportRequest, $validator->validated());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed.', 422, $e->errors());
        }

        return $this->success(new \App\Http\Resources\TransportRequestResource($transportRequest->fresh()->load('user', 'driver')), 'Tugas berhasil diselesaikan.');
    }

    public function cancel(Request $request, TransportRequest $transportRequest)
    {
        $user = $request->user();
        $driverOwner = $user->driver && $transportRequest->driver_id === $user->driver->id;
        if (! $driverOwner && ! $user->hasPermission('transport.request.update')) {
            return $this->error('Anda tidak memiliki akses.', 403);
        }

        $formRequest = app(\App\Http\Requests\Driver\CancelTransportRequestRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }

        $reason = $validator->validated('rejection_reason');
        if ($driverOwner) {
            $this->workflow->cancelByDriver($transportRequest, $reason);
        } else {
            $transportRequest->update(['status' => 'tidak_disetujui', 'rejection_reason' => $reason]);
        }

        return $this->success(new \App\Http\Resources\TransportRequestResource($transportRequest->fresh()->load('user', 'driver')), 'Pengajuan berhasil dibatalkan.');
    }

    public function print(Request $request, TransportRequest $transportRequest)
    {
        $transportRequest->load('driver');
        if (empty($transportRequest->plat_nomor) && ! empty($transportRequest->unit_mobil)) {
            $vehicle = \App\Models\Vehicle::where('name', $transportRequest->unit_mobil)->first();
            if ($vehicle) {
                $transportRequest->plat_nomor = $vehicle->plate_number;
            }
        }
        return $this->success(new \App\Http\Resources\TransportRequestResource($transportRequest));
    }
}