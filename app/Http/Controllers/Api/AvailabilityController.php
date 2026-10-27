<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Transport\CheckAvailabilityRequest;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AvailabilityController extends BaseApiController
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function umum(Request $request)
    {
        $formRequest = app(CheckAvailabilityRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }
        $data = $validator->validated();

        $tr = new \App\Models\TransportRequest([
            'jenis' => 'umum',
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'],
            'tanggal_sampai' => $data['tanggal_sampai'] ?? null,
            'jam_sampai' => $data['jam_sampai'] ?? null,
        ]);
        $available = $this->availability->isAvailable('umum', $tr);

        return $this->success(['available' => $available]);
    }

    public function ambulance(Request $request)
    {
        $formRequest = app(CheckAvailabilityRequest::class);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());
        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }
        $data = $validator->validated();

        $tr = new \App\Models\TransportRequest([
            'jenis' => 'ambulance',
            'tanggal' => $data['tanggal'],
            'jam' => $data['jam'],
            'tanggal_sampai' => $data['tanggal_sampai'] ?? null,
            'jam_sampai' => $data['jam_sampai'] ?? null,
        ]);
        $available = $this->availability->isAvailable('ambulance', $tr);

        return $this->success(['available' => $available]);
    }
}