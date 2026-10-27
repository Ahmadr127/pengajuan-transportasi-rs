<?php

namespace App\Http\Controllers\Api;

use App\Models\TransportRequest;
use App\Services\SignatureService;
use Illuminate\Http\Request;

class SignatureController extends BaseApiController
{
    public function __construct(private readonly SignatureService $signatures) {}

    public function show(Request $request, TransportRequest $transportRequest)
    {
        $user = $request->user();
        $type = $this->signatures->determineType($transportRequest, $user);
        if (! $type) {
            return $this->error('Tidak ada tanda tangan yang diperlukan saat ini.', 422);
        }
        return $this->success([
            'signature_type' => $type,
            'back_url' => $this->signatures->backUrl($transportRequest, $user, $type),
        ]);
    }

    public function sign(Request $request, TransportRequest $transportRequest)
    {
        $user = $request->user();
        $type = $this->signatures->determineType($transportRequest, $user);
        if (! $type) {
            return $this->error('Tidak ada tanda tangan yang diperlukan saat ini.', 422);
        }
        $result = $this->signatures->sign($transportRequest, $type, $user);
        return $this->success([
            'message' => $result['message'],
            'redirect' => $result['redirect'],
        ], $result['message']);
    }

    public function verify(string $code)
    {
        $transportRequest = TransportRequest::where('signature_pemohon', $code)
            ->orWhere('signature_pengelola_1', $code)
            ->orWhere('signature_driver', $code)
            ->orWhere('signature_pengelola_2', $code)
            ->first();

        if (! $transportRequest) {
            return $this->error('Tanda tangan tidak ditemukan.', 404);
        }

        $signatureInfo = null;
        if ($transportRequest->signature_pemohon === $code) {
            $signatureInfo = ['role' => 'Pemohon', 'name' => $transportRequest->user->full_name ?? $transportRequest->pemohon_nama, 'signed_at' => $transportRequest->signature_pemohon_at];
        } elseif ($transportRequest->signature_pengelola_1 === $code) {
            $signatureInfo = ['role' => 'Pengelola Transportasi - Menyetujui', 'name' => $transportRequest->signature_pengelola_1_name, 'signed_at' => $transportRequest->signature_pengelola_1_at];
        } elseif ($transportRequest->signature_driver === $code) {
            $signatureInfo = ['role' => 'Pengemudi', 'name' => $transportRequest->driver->name ?? '-', 'signed_at' => $transportRequest->signature_driver_at];
        } elseif ($transportRequest->signature_pengelola_2 === $code) {
            $signatureInfo = ['role' => 'Pengelola Transportasi - Mengetahui', 'name' => $transportRequest->signature_pengelola_2_name, 'signed_at' => $transportRequest->signature_pengelola_2_at];
        }

        return $this->success([
            'found' => true,
            'transport_request' => new \App\Http\Resources\TransportRequestResource($transportRequest->load('user', 'driver')),
            'signature' => $signatureInfo,
        ]);
    }
}