<?php

namespace App\Services;

use App\Models\TransportRequest;
use App\Models\User;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SignatureService
{
    /**
     * Tentukan tipe tanda tangan yang relevan untuk user ini pada pengajuan ini.
     */
    public function determineType(TransportRequest $transportRequest, User $user): ?string
    {
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

    public function backUrl(TransportRequest $transportRequest, User $user, ?string $signatureType = null): string
    {
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

    /**
     * Simpan tanda tangan sesuai tipe.
     *
     * @return array{message: string, redirect: string}
     */
    public function sign(TransportRequest $transportRequest, string $type, User $user): array
    {
        $code = Str::random(32);

        switch ($type) {
            case 'pemohon':
                $transportRequest->update(['signature_pemohon' => $code, 'signature_pemohon_at' => now()]);
                $message = 'Tanda tangan pemohon berhasil disimpan.';
                $redirect = $user->hasPermission('transport.request.view')
                    ? route('admin.transport.index')
                    : route('pengajuan.index');
                break;

            case 'pengelola_1':
                $transportRequest->update([
                    'signature_pengelola_1' => $code,
                    'signature_pengelola_1_at' => now(),
                    'signature_pengelola_1_name' => $user->full_name,
                ]);
                $message = 'Tanda tangan pengelola berhasil disimpan.';
                $redirect = route('admin.transport.show', $transportRequest);
                break;

            case 'driver':
                $transportRequest->update(['signature_driver' => $code, 'signature_driver_at' => now()]);
                $message = 'Tanda tangan pengemudi berhasil disimpan.';
                $redirect = $user->hasPermission('driver.task.view')
                    ? route('driver.dashboard')
                    : route('admin.transport.show', $transportRequest);
                break;

            case 'pengelola_2':
                $transportRequest->update([
                    'signature_pengelola_2' => $code,
                    'signature_pengelola_2_at' => now(),
                    'signature_pengelola_2_name' => $user->full_name,
                ]);
                $message = 'Tanda tangan pengelola berhasil disimpan.';
                $redirect = route('admin.transport.show', $transportRequest);
                break;

            default:
                throw new InvalidArgumentException('Tipe tanda tangan tidak valid.');
        }

        return ['message' => $message, 'redirect' => $redirect];
    }
}
