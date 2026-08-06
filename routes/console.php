<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan command untuk auto-reject expired requests setiap jam
Schedule::command('transport:auto-reject-expired')->hourly();

// Jadwalkan pembentukan pengajuan berulang setiap jam 12 malam
Schedule::command('app:generate-recurring-requests')->dailyAt('00:05');
