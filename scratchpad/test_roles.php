<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['super-admin','admin','driver','user'] as $r) {
    $c = App\Models\User::whereHas('roles', fn($q) => $q->where('name', $r))->count();
    echo $r.': '.$c.PHP_EOL;
}
echo "users with no pivot roles: ".App\Models\User::doesntHave('roles')->count().PHP_EOL;
echo "role column dropped (no longer queried)\n";
