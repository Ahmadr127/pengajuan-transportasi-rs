<?php

use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\JobRoleController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RecurringTemplateController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SignatureController;
use App\Http\Controllers\Api\TransportRequestController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('api.auth.login');

Route::get('/signatures/verify/{code}', [SignatureController::class, 'verify'])
    ->name('api.signatures.verify');

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');

    // Users
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:user.manage')->name('api.users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('permission:user.manage')->name('api.users.show');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:user.manage')->name('api.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:user.manage')->name('api.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:user.manage')->name('api.users.destroy');
    Route::post('/users/sync-simutu', [UserController::class, 'syncFromSimutu'])->middleware('permission:user.sync')->name('api.users.sync');

    // Vehicles
    Route::apiResource('vehicles', VehicleController::class)->middleware('permission:vehicle.manage');

    // Drivers
    Route::apiResource('drivers', DriverController::class)->middleware('permission:driver.manage');

    // Units
    Route::apiResource('units', UnitController::class)->except(['show'])->middleware('permission:unit.manage');

    // Job roles
    Route::apiResource('job-roles', JobRoleController::class)->except(['show'])->middleware('permission:jabatan.manage');

    // Roles (ACL)
    Route::apiResource('roles', RoleController::class)->middleware('permission:role.manage');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->middleware('permission:role.manage')->name('api.permissions.index');

    // Menus
    Route::apiResource('menus', MenuController::class)->middleware('permission:menu.manage');

    // Transport requests
    Route::get('/transport-requests', [TransportRequestController::class, 'index'])->name('api.transport-requests.index');
    Route::get('/transport-requests/{transportRequest}', [TransportRequestController::class, 'show'])->name('api.transport-requests.show');
    Route::post('/transport-requests', [TransportRequestController::class, 'store'])->middleware('permission:transport.request.create')->name('api.transport-requests.store');
    Route::put('/transport-requests/{transportRequest}', [TransportRequestController::class, 'update'])->middleware('permission:transport.request.update')->name('api.transport-requests.update');
    Route::post('/transport-requests/{transportRequest}/complete', [TransportRequestController::class, 'complete'])->middleware('permission:driver.task.complete')->name('api.transport-requests.complete');
    Route::post('/transport-requests/{transportRequest}/cancel', [TransportRequestController::class, 'cancel'])->name('api.transport-requests.cancel');
    Route::get('/transport-requests/{transportRequest}/print', [TransportRequestController::class, 'print'])->middleware('permission:transport.request.print')->name('api.transport-requests.print');

    // Availability
    Route::get('/availability/umum', [AvailabilityController::class, 'umum'])->middleware('permission:transport.request.create')->name('api.availability.umum');
    Route::get('/availability/ambulance', [AvailabilityController::class, 'ambulance'])->middleware('permission:transport.request.create')->name('api.availability.ambulance');

    // Recurring templates
    Route::apiResource('recurring-templates', RecurringTemplateController::class)->only(['index', 'show', 'update', 'destroy'])->middleware('permission:recurring.manage');

    // Signatures
    Route::get('/signatures/{transportRequest}', [SignatureController::class, 'show'])->middleware('permission:signature.sign')->name('api.signatures.show');
    Route::post('/signatures/{transportRequest}', [SignatureController::class, 'sign'])->middleware('permission:signature.sign')->name('api.signatures.sign');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');

    // Reports
    Route::get('/reports/transport', [ReportController::class, 'index'])->middleware('permission:report.view')->name('api.reports.transport');
    Route::get('/reports/transport/export', [ReportController::class, 'export'])->middleware('permission:report.export')->name('api.reports.export');
});