<?php

namespace App\Http\Controllers;

use App\Models\TransportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    public function show(TransportRequest $transportRequest)
    {
        $signatureType = $this->determineSignatureType($transportRequest);
        if (! $signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }

        return view('signature.show', [
            'transportRequest' => $transportRequest,
            'signatureType' => $signatureType,
            'backUrl' => $this->backUrl($transportRequest, $signatureType),
        ]);
    }

    public function confirm(TransportRequest $transportRequest)
    {
        $signatureType = $this->determineSignatureType($transportRequest);
        if (! $signatureType) {
            return view('signature.already_signed', [
                'transportRequest' => $transportRequest,
                'backUrl' => $this->backUrl($transportRequest),
            ]);
        }

        return view('signature.confirm', [
            'transportRequest' => $transportRequest,
            'signatureType' => $signatureType,
            'backUrl' => $this->backUrl($transportRequest, $signatureType),
        ]);
    }

    public function sign(Request $request, TransportRequest $transportRequest)
    {
        $signatureType = $this->determineSignatureType($transportRequest);
        if (! $signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }

        $user = auth()->user();
        $signatureCode = Str::random(32);

        switch ($signatureType) {
            case 'pemohon':
                $transportRequest->update(['signature_pemohon' => $signatureCode, 'signature_pemohon_at' => now()]);
                $message = 'Tanda tangan pemohon berhasil disimpan.';
                $redirect = $user->hasPermission('transport.request.view')
                    ? route('admin.transport.index')
                    : route('pengajuan.index');
                break;
            case 'pengelola_1':
                $transportRequest->update(['signature_pengelola_1' => $signatureCode, 'signature_pengelola_1_at' => now(), 'signature_pengelola_1_name' => $user->full_name]);
                $message = 'Tanda tangan pengelola berhasil disimpan.';
                $redirect = route('admin.transport.show', $transportRequest);
                break;
            case 'driver':
                $transportRequest->update(['signature_driver' => $signatureCode, 'signature_driver_at' => now()]);
                $message = 'Tanda tangan pengemudi berhasil disimpan.';
                $redirect = $user->hasPermission('driver.task.view')
                    ? route('driver.dashboard')
                    : route('admin.transport.show', $transportRequest);
                break;
            case 'pengelola_2':
                $transportRequest->update(['signature_pengelola_2' => $signatureCode, 'signature_pengelola_2_at' => now(), 'signature_pengelola_2_name' => $user->full_name]);
                $message = 'Tanda tangan pengelola berhasil disimpan.';
                $redirect = route('admin.transport.show', $transportRequest);
                break;
            default:
                return redirect()->back()->with('error', 'Tipe tanda tangan tidak valid.');
        }

        return redirect($redirect)->with('success', $message);
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
                'role'      => 'Pemohon',
                'name'      => $transportRequest->user->full_name ?? $transportRequest->pemohon_nama,
                'unit'      => $transportRequest->user->unit_kerja ?? $transportRequest->pemohon_unit,
                'signed_at' => $transportRequest->signature_pemohon_at,
            ];
        } elseif ($transportRequest->signature_pengelola_1 === $code) {
            $signatureInfo = [
                'role'      => 'Pengelola Transportasi - Menyetujui',
                'name'      => $transportRequest->signature_pengelola_1_name,
                'unit'      => 'Pengelola Transportasi',
                'signed_at' => $transportRequest->signature_pengelola_1_at,
            ];
        } elseif ($transportRequest->signature_driver === $code) {
            $signatureInfo = [
                'role'      => 'Pengemudi',
                'name'      => $transportRequest->driver->name ?? '-',
                'unit'      => $transportRequest->driver->phone ?? '-',
                'signed_at' => $transportRequest->signature_driver_at,
            ];
        } elseif ($transportRequest->signature_pengelola_2 === $code) {
            $signatureInfo = [
                'role'      => 'Pengelola Transportasi - Mengetahui',
                'name'      => $transportRequest->signature_pengelola_2_name,
                'unit'      => 'Pengelola Transportasi',
                'signed_at' => $transportRequest->signature_pengelola_2_at,
            ];
        }

        return view('signature.verify', [
            'found'            => true,
            'transportRequest' => $transportRequest,
            'signatureInfo'    => $signatureInfo,
        ]);
    }

    private function backUrl(TransportRequest $transportRequest, ?string $signatureType = null): string
    {
        $user = auth()->user();

        if ($signatureType === 'driver' && $user->hasPermission('driver.task.view')) {
            return route('driver.dashboard');
        }

        if ($user->hasPermission('transport.request.view')) {
            return route('admin.transport.show', $transportRequest);
        }

        if ($user->hasPermission('driver.task.view')) {
            return route('driver.dashboard');
        }

        return route('pengajuan.index');
    }

    private function determineSignatureType(TransportRequest $transportRequest): ?string
    {
        $user = auth()->user();

        $isAssignedDriver = $user->driver
            && $transportRequest->driver_id === $user->driver->id;

        if ($transportRequest->status === 'diajukan' && ! $transportRequest->signature_pemohon && $transportRequest->user_id === $user->id) {
            return 'pemohon';
        }
        if ($transportRequest->status === 'diproses' && ! $transportRequest->signature_pengelola_1 && $user->hasPermission('transport.request.approve')) {
            return 'pengelola_1';
        }
        if ($transportRequest->status === 'digunakan' && ! $transportRequest->signature_driver && ($isAssignedDriver || $user->hasPermission('transport.request.update'))) {
            return 'driver';
        }
        if ($transportRequest->status === 'selesai' && ! $transportRequest->signature_pengelola_2 && $user->hasPermission('transport.request.complete')) {
            return 'pengelola_2';
        }

        return null;
    }
}
