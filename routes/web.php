<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\TransportRequestController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TransportRequestController as AdminTransportController;
use App\Services\LandingResolver;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return app(LandingResolver::class)->redirectFor(auth()->user());
})->name('home');

// Auth
Route::get('/login', [AuthController::class, 'create'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])
    ->name('logout');

// QR Verify Route — bisa diakses tanpa login (untuk scan dari print surat)
Route::get('/verify/{code}', [SignatureController::class, 'verify'])->name('signature.verify');

// Aplikasi Pengajuan Transportasi (harus login)
Route::middleware('auth')->group(function () {
    // Setelah login, langsung masuk halaman pilih jenis transport
    Route::get('/dashboard', [TransportRequestController::class, 'choose'])
        ->middleware('permission:transport.request.create')
        ->name('dashboard');

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::middleware('permission:transport.request.create')->group(function () {
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
        });

        Route::get('/sukses/{transportRequest}', [TransportRequestController::class, 'success'])
            ->middleware('permission:transport.request.history')
            ->name('success');
        Route::get('/sukses/{transportRequest}/print', [TransportRequestController::class, 'print'])
            ->middleware('permission:transport.request.print')
            ->name('print');
        Route::get('/riwayat', [TransportRequestController::class, 'index'])
            ->middleware('permission:transport.request.history')
            ->name('index');
    });

    // Profil User
    Route::get('/profil', [TransportRequestController::class, 'profil'])
        ->middleware('permission:profile.view')
        ->name('profil');

    // Halaman fallback bila user tidak punya menu/permission sama sekali.
    // Route::view('/tidak-ada-akses', 'errors.no-access')
    //     ->name('no.access');

    // Signature Routes
    Route::middleware('permission:signature.sign')->group(function () {
        Route::get('/signature/{transportRequest}', [SignatureController::class, 'show'])->name('signature.show');
        Route::get('/signature/{transportRequest}/confirm', [SignatureController::class, 'confirm'])->name('signature.confirm');
        Route::post('/signature/{transportRequest}', [SignatureController::class, 'sign'])->name('signature.sign');
    });
});

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminTransportController::class, 'dashboard'])
        ->middleware('permission:dashboard.admin.view')
        ->name('dashboard');

    // Transport Requests
    Route::get('/transport', [AdminTransportController::class, 'index'])
        ->middleware('permission:transport.request.view')
        ->name('transport.index');
    Route::get('/transport/{transportRequest}', [AdminTransportController::class, 'show'])
        ->middleware('permission:transport.request.view')
        ->name('transport.show');
    Route::put('/transport/{transportRequest}', [AdminTransportController::class, 'update'])
        ->middleware('permission:transport.request.update')
        ->name('transport.update');
    Route::get('/transport/{transportRequest}/print', [AdminTransportController::class, 'print'])
        ->middleware('permission:transport.request.print')
        ->name('transport.print');

    Route::get('/laporan', [AdminTransportController::class, 'laporan'])
        ->middleware('permission:report.view')
        ->name('laporan');
    Route::get('/laporan-export', [AdminTransportController::class, 'laporanExport'])
        ->middleware('permission:report.export')
        ->name('laporan.export');
    Route::get('/laporan-print', [AdminTransportController::class, 'laporanPrint'])
        ->middleware('permission:report.print')
        ->name('laporan.print');
    Route::get('/laporan/{transportRequest}', [AdminTransportController::class, 'laporanDetail'])
        ->middleware('permission:report.view')
        ->name('laporan.detail');

    // Master Data
    Route::post('users/sync-simutu', [\App\Http\Controllers\Admin\UserController::class, 'syncFromSimutu'])
        ->middleware('permission:user.sync')
        ->name('users.sync-simutu');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
        ->middleware('permission:user.manage');
    Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class)
        ->middleware('permission:vehicle.manage');
    Route::resource('drivers', \App\Http\Controllers\Admin\DriverController::class)
        ->middleware('permission:driver.manage');
    Route::resource('recurring-templates', \App\Http\Controllers\Admin\RecurringTransportTemplateController::class)
        ->middleware('permission:recurring.manage');

    // Pengaturan RBAC
    Route::resource('roles', RoleController::class)
        ->except(['show'])
        ->middleware('permission:role.manage');
    Route::resource('menus', MenuController::class)
        ->except(['show'])
        ->middleware('permission:menu.manage');
});

// Driver Routes
Route::middleware('auth')->prefix('driver')->name('driver.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Driver\DashboardController::class, 'index'])
        ->middleware('permission:driver.task.view')
        ->name('dashboard');
    Route::get('/history', [\App\Http\Controllers\Driver\DashboardController::class, 'history'])
        ->middleware('permission:driver.task.history')
        ->name('history');
    Route::get('/profil', [\App\Http\Controllers\Driver\DashboardController::class, 'profil'])
        ->middleware('permission:driver.task.view')
        ->name('profil');
    Route::get('/detail/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'detail'])
        ->middleware('permission:driver.task.view')
        ->name('detail');
    Route::get('/detail/{transportRequest}/print', [\App\Http\Controllers\Driver\DashboardController::class, 'print'])
        ->middleware('permission:driver.task.print')
        ->name('print');
    Route::post('/complete/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'complete'])
        ->middleware('permission:driver.task.complete')
        ->name('complete');
    Route::post('/cancel/{transportRequest}', [\App\Http\Controllers\Driver\DashboardController::class, 'cancel'])
        ->middleware('permission:driver.task.cancel')
        ->name('cancel');
});
