<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::query()->latest();
        $like  = fn($val) => '%' . strtolower($val) . '%';

        if ($request->filled('nama'))    $query->whereRaw('LOWER(name) LIKE ?', [$like($request->nama)]);
        if ($request->filled('telepon')) $query->whereRaw('LOWER(phone) LIKE ?', [$like($request->telepon)]);
        if ($request->filled('sim'))     $query->whereRaw('LOWER(license_number) LIKE ?', [$like($request->sim)]);
        if ($request->input('is_active') !== '' && $request->has('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }

        $drivers = $query->paginate(15)->withQueryString();
        $driverUsers = \App\Models\User::where('role', 'driver')->orderBy('first_name')->get();
        return view('admin.drivers.index', compact('drivers', 'driverUsers'));
    }

    public function create()
    {
        $driverUsers = \App\Models\User::where('role', 'driver')->orderBy('first_name')->get();
        return view('admin.drivers.create', compact('driverUsers'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.drivers.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        Driver::create($data);

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Supir berhasil ditambahkan.');
    }

    public function edit(Driver $driver)
    {
        $driverUsers = \App\Models\User::where('role', 'driver')->orderBy('first_name')->get();
        return view('admin.drivers.edit', compact('driver', 'driverUsers'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'exists:users,id'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.drivers.edit', $driver)
                ->withErrors($validator)
                ->withInput();
        }

        $driver->update($validator->validated());

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
