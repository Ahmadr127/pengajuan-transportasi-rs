<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$q = App\Models\User::query();
$q->whereHas('roles', fn ($r) => $r->where('name', 'user'));
echo "count user role: " . $q->count() . PHP_EOL;
echo "total users: " . App\Models\User::count() . PHP_EOL;

// simula request role=admin
$q2 = App\Models\User::query();
$q2->whereHas('roles', fn ($r) => $r->where('name', 'admin'));
echo "count admin role: " . $q2->count() . PHP_EOL;
