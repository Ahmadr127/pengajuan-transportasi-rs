<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDriverRequest;
use App\Http\Requests\Admin\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query()->latest();
        $like = fn ($val) => '%'.strtolower($val).'%';

        if ($request->filled('nama')) {
            $query->whereRaw('LOWER(name) LIKE ?', [$like($request->nama)]);
        }
        if ($request->filled('telepon')) {
            $query->whereRaw('LOWER(phone) LIKE ?', [$like($request->telepon)]);
        }
        if ($request->filled('sim')) {
            $query->whereRaw('LOWER(license_number) LIKE ?', [$like($request->sim)]);
        }
        if ($request->input('is_active') !== '' && $request->has('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }

        $drivers = $query->paginate(15)->withQueryString();
        $driverUsers = User::withPermission('driver.task.view')->orderBy('first_name')->get();

        return view('admin.drivers.index', compact('drivers', 'driverUsers'));
    }

    public function create()
    {
        $driverUsers = User::withPermission('driver.task.view')->orderBy('first_name')->get();

        return view('admin.drivers.create', compact('driverUsers'));
    }

    public function store(StoreDriverRequest $request)
    {
        Driver::create($request->validated());

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Supir berhasil ditambahkan.');
    }

    public function edit(Driver $driver)
    {
        $driverUsers = User::withPermission('driver.task.view')->orderBy('first_name')->get();

        return view('admin.drivers.edit', compact('driver', 'driverUsers'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->validated());

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Supir berhasil diupdate.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Supir berhasil dihapus.');
    }
}
