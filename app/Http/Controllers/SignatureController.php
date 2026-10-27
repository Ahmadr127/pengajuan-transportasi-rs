<?php

namespace App\Http\Controllers;

use App\Models\TransportRequest;
use App\Services\SignatureService;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function __construct(private readonly SignatureService $signatures) {}

    public function show(TransportRequest $transportRequest)
    {
        $user = auth()->user();
        $signatureType = $this->signatures->determineType($transportRequest, $user);

        if (! $signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }

        return view('signature.show', [
            'transportRequest' => $transportRequest,
            'signatureType' => $signatureType,
            'backUrl' => $this->signatures->backUrl($transportRequest, $user, $signatureType),
        ]);
    }

    public function confirm(TransportRequest $transportRequest)
    {
        $user = auth()->user();
        $signatureType = $this->signatures->determineType($transportRequest, $user);

        if (! $signatureType) {
            return view('signature.already_signed', [
                'transportRequest' => $transportRequest,
                'backUrl' => $this->signatures->backUrl($transportRequest, $user),
            ]);
        }

        return view('signature.confirm', [
            'transportRequest' => $transportRequest,
            'signatureType' => $signatureType,
            'backUrl' => $this->signatures->backUrl($transportRequest, $user, $signatureType),
        ]);
    }

    public function sign(Request $request, TransportRequest $transportRequest)
    {
        $user = auth()->user();
        $signatureType = $this->signatures->determineType($transportRequest, $user);

        if (! $signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }

        $result = $this->signatures->sign($transportRequest, $signatureType, $user);

        return redirect($result['redirect'])->with('success', $result['message']);
    }

    public function verify(string $code)
    {
        $transportRequest = TransportRequest::where('signature_pemohon', $code)
            ->orWhere('signature_pengelola_1', $code)
            ->orWhere('signature_driver', $code)
            ->orWhere('signature_pengelola_2', $code)
            ->first();

        if (! $transportRequest) {
            return view('signature.verify', ['found' => false, 'code' => $code]);
        }

        $signatureInfo = null;
        if ($transportRequest->signature_pemohon === $code) {
            $signatureInfo = [
                'role' => 'Pemohon',
                'name' => $transportRequest->user->full_name ?? $transportRequest->pemohon_nama,
                'unit' => $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit,
                'signed_at' => $transportRequest->signature_pemohon_at,
            ];
        } elseif ($transportRequest->signature_pengelola_1 === $code) {
            $signatureInfo = [
                'role' => 'Pengelola Transportasi - Menyetujui',
                'name' => $transportRequest->signature_pengelola_1_name,
                'unit' => 'Pengelola Transportasi',
                'signed_at' => $transportRequest->signature_pengelola_1_at,
            ];
        } elseif ($transportRequest->signature_driver === $code) {
            $signatureInfo = [
                'role' => 'Pengemudi',
                'name' => $transportRequest->driver->name ?? '-',
                'unit' => $transportRequest->driver->phone ?? '-',
                'signed_at' => $transportRequest->signature_driver_at,
            ];
        } elseif ($transportRequest->signature_pengelola_2 === $code) {
            $signatureInfo = [
                'role' => 'Pengelola Transportasi - Mengetahui',
                'name' => $transportRequest->signature_pengelola_2_name,
                'unit' => 'Pengelola Transportasi',
                'signed_at' => $transportRequest->signature_pengelola_2_at,
            ];
        }

        return view('signature.verify', [
            'found' => true,
            'transportRequest' => $transportRequest,
            'signatureInfo' => $signatureInfo,
        ]);
    }
}
