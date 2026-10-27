<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->latest();
        $like = fn ($val) => '%'.strtolower($val).'%';

        if ($request->filled('nama')) {
            $query->whereRaw('LOWER(name) LIKE ?', [$like($request->nama)]);
        }
        if ($request->filled('plat')) {
            $query->whereRaw('LOWER(plate_number) LIKE ?', [$like($request->plat)]);
        }
        if ($request->filled('merk')) {
            $query->whereRaw('LOWER(CONCAT(COALESCE(brand,\'\'), \' \', COALESCE(model,\'\'))) LIKE ?', [$like($request->merk)]);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->input('is_active') !== '' && $request->has('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }

        $vehicles = $query->paginate(15)->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function store(StoreVehicleRequest $request)
    {
        Vehicle::create($request->validated());

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil diupdate.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}
