<?php

use App\Models\TransportRequest;
use App\Models\User;
use App\Services\TransportRequestQueryService;

$query = app(TransportRequestQueryService::class);

echo 'total rows: '.TransportRequest::count().PHP_EOL;

foreach (TransportRequest::selectRaw('id, user_id, jenis, status, tanggal, jam, nomor_pengajuan')->get() as $row) {
    echo sprintf(
        "#%d user=%s jenis=%s status=%s tanggal=%s jam=%s nomor=%s\n",
        $row->id,
        (string) $row->user_id,
        (string) $row->jenis,
        (string) $row->status,
        (string) $row->tanggal,
        (string) $row->jam,
        (string) $row->nomor_pengajuan
    );
}

$page = $query->adminIndex([]);
echo 'adminIndex total='.$page->total().' count='.$page->count().' ids='.implode(',', $page->pluck('id')->all()).PHP_EOL;

foreach (User::with('roles', 'driver')->get() as $user) {
    echo sprintf(
        "%-18s roles=%-12s driver_id=%-4s own_requests=%d perms=%s\n",
        $user->username,
        $user->roles->pluck('name')->implode(','),
        $user->driver?->id ?? '-',
        TransportRequest::where('user_id', $user->id)->count(),
        implode(',', array_slice($user->permissionNames(), 0, 3))
    );
}
