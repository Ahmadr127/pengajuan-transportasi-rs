<?php

use App\Models\Role;
use App\Models\User;

foreach (Role::with('permissions')->orderBy('id')->get() as $role) {
    $perms = $role->permissions->pluck('name');
    echo sprintf(
        "%-12s dashboard.admin.view=%-5s transport.request.view=%-5s driver.task.view=%-5s perms=%d\n",
        $role->name,
        $perms->contains('dashboard.admin.view') ? 'yes' : 'no',
        $perms->contains('transport.request.view') ? 'yes' : 'no',
        $perms->contains('driver.task.view') ? 'yes' : 'no',
        $perms->count()
    );
}

echo PHP_EOL.'users tanpa transport.request.view tapi punya dashboard.admin.view:'.PHP_EOL;

foreach (User::with('roles.permissions')->get() as $user) {
    $perms = $user->permissionNames();
    if (in_array('dashboard.admin.view', $perms, true) && ! in_array('transport.request.view', $perms, true)) {
        echo ' - '.$user->username.' roles='.$user->roles->pluck('name')->implode(',').PHP_EOL;
    }
}
