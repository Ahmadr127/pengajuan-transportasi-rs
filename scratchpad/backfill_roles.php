<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    if ($u->roles()->count() === 0 && $u->role) {
        $u->syncRolesByName([$u->role]);
        echo "backfilled: {$u->username} -> {$u->role}\n";
    }
}
echo "done\n";
