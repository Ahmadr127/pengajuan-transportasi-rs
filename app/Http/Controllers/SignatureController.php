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
        if (!$signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }
        return view('signature.show', compact('transportRequest', 'signatureType'));
    }

    public function confirm(TransportRequest $transportRequest)
    {
        $signatureType = $this->determineSignatureType($transportRequest);
        if (!$signatureType) {
            return view('signature.already_signed', compact('transportRequest'));
        }
        return view('signature.confirm', compact('transportRequest', 'signatureType'));
    }

    public function sign(Request $request, TransportRequest $transportRequest)
    {
        $signatureType = $this->determineSignatureType($transportRequest);
        if (!$signatureType) {
            return redirect()->back()->with('error', 'Tidak ada tanda tangan yang diperlukan saat ini.');
        }
        $signatureCode = Str::random(32);
        switch ($signatureType) {
            case 'pemohon':
                $transportRequest->update(['signature_pemohon' => $signatureCode, 'signature_pemohon_at' => now()]);
                $message = 'Tanda tangan pemohon berhasil disimpan.';
                $redirect = auth()->user()->isAdmin() ? route('admin.transport.index') : route('pengajuan.index');
                break;
            case 'pengelola_1':
                $transportRequest->update(['signature_pengelola_1' => $signatureCode, 'signature_pengelola_1_at' => now(), 'signature_pengelola_1_name' => auth()->user()->full_name]);
                $message = 'Tanda tangan pengelola berhasil disimpan.';
                $redirect = route('admin.transport.show', $transportRequest);
                break;
            case 'driver':
                $transportRequest->update(['signature_driver' => $signatureCode, 'signature_driver_at' => now()]);
                $message = 'Tanda tangan pengemudi berhasil disimpan.';
                $redirect = auth()->user()->isDriver()
                    ? route('driver.dashboard')
                    : route('admin.transport.show', $transportRequest);
                break;
            case 'pengelola_2':
                $transportRequest->update(['signature_pengelola_2' => $signatureCode, 'signature_pengelola_2_at' => now(), 'signature_pengelola_2_name' => auth()->user()->full_name]);
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

        if (!$transportRequest) {
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

    private function determineSignatureType(TransportRequest $transportRequest)
    {
        $user = auth()->user();

        // Cek apakah user adalah supir yang ditugaskan
        $isAssignedDriver = false;
        if ($user->isDriver() && $user->driver) {
            $isAssignedDriver = $transportRequest->driver_id === $user->driver->id;
        }

        if ($transportRequest->status === 'diajukan' && !$transportRequest->signature_pemohon && $transportRequest->user_id === $user->id) return 'pemohon';
        if ($transportRequest->status === 'diproses' && !$transportRequest->signature_pengelola_1 && $user->isAdmin()) return 'pengelola_1';
        if ($transportRequest->status === 'digunakan' && !$transportRequest->signature_driver && ($user->isAdmin() || $isAssignedDriver)) return 'driver';
        if ($transportRequest->status === 'selesai' && !$transportRequest->signature_pengelola_2 && $user->isAdmin()) return 'pengelola_2';
        return null;
    }
}