<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransportRequestController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\Admin\TransportRequestController as AdminTransportController;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->isDriver()) {
        return redirect()->route('driver.dashboard');
    }
    return redirect()->route('dashboard');
})->name('home');

// Auth
Route::get('/login', [AuthController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])
    ->name('logout');

// QR Verify Route — bisa diakses tanpa login (untuk scan dari print surat)
Route::get('/verify/{code}', [SignatureController::class, 'verify'])->name('signature.verify');

// Aplikasi Pengajuan Transportasi (harus login)
Route::middleware('auth')->group(function () {
    // Setelah login, langsung masuk halaman pilih jenis transport
    Route::get('/dashboard', [TransportRequestController::class, 'choose'])
        ->name('dashboard');

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/', [TransportRequestController::class, 'choose'])
            ->name('choose');

        // Umum
        Route::get('/umum', [TransportRequestController::class, 'createUmum'])
            ->name('umum.create');
        Route::post('/umum', [TransportRequestController::class, 'storeUmum'])
            ->name('umum.store');

        // API: check availability for mobil umum
        Route::get('/umum/check-availability', [TransportRequestController::class, 'checkAvailability'])
            ->name('umum.check');

        // Ambulance - langsung ke satu form, pilihan antar/jemput ada di form
        Route::get('/ambulance', [TransportRequestController::class, 'createAmbulance'])
            ->name('ambulance.create');
        // API: check availability for ambulance unit
        Route::get('/ambulance/check-availability', [TransportRequestController::class, 'checkAmbulanceAvailability'])
            ->name('ambulance.check');
        Route::post('/ambulance', [TransportRequestController::class, 'storeAmbulance'])
            ->name('ambulance.store');

        Route::get('/sukses/{transportRequest}', [TransportRequestController::class, 'success'])
            ->name('success');
        Route::get('/sukses/{transportRequest}/print', [TransportRequestController::class, 'print'])
            ->name('print');
        Route::get('/riwayat', [TransportRequestController::class, 'index'])
            ->name('index');
    });

    // Profil User
    Route::get('/profil', [TransportRequestController::class, 'profil'])->name('profil');
    
    // Signature Routes
    Route::get('/signature/{transportRequest}', [SignatureController::class, 'show'])->name('signature.show');
    Route::get('/signature/{transportRequest}/confirm', [SignatureController::class, 'confirm'])->name('signature.confirm');
    Route::post('/signature/{transportRequest}', [SignatureController::class, 'sign'])->name('signature.sign');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminTransportController::class, 'dashboard'])->name('dashboard');
    
    // Transport Requests
    Route::get('/transport', [AdminTransportController::class, 'index'])->name('transport.index');
    Route::get('/transport/{transportRequest}', [AdminTransportController::class, 'show'])->name('transport.show');
    Route::put('/transport/{transportRequest}', [AdminTransportController::class, 'update'])->name('transport.update');
    Route::get('/transport/{transportRequest}/print', [AdminTransportController::class, 'print'])->name('transport.print');

    Route::get('/laporan', [AdminTransportController::class, 'laporan'])->name('laporan');
    Route::get('/laporan-export', [AdminTransportController::class, 'laporanExport'])->name('laporan.export');
    Route::get('/laporan-print', [AdminTransportController::class, 'laporanPrint'])->name('laporan.print');
    Route::get('/laporan/{transportRequest}', [AdminTransportController::class, 'laporanDetail'])->name('laporan.detail');
    
    // Master Data
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class);
    Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class);
    Route::resource('recurring-templates', \App\Http\Controllers\Admin\RecurringTransportTemplateController::class);
});

// Driver Routes
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Driver\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [\App\Http\Controllers\Driver\DashboardController::class, 'history'])->name('history');
    Route::get('/profil', [\App\Http\Controllers\Driver\DashboardController::class, 'profil'])->name('profil');
    Route::get('/detail/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'detail'])->name('detail');
    Route::get('/detail/{transportRequest}/print', [\App\Http\Controllers\Driver\DashboardController::class, 'print'])->name('print');
    Route::post('/complete/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'complete'])->name('complete');
    Route::post('/cancel/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'cancel'])->name('cancel');
});
