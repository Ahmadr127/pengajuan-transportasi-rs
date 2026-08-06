<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->latest();
        $like  = fn($val) => '%' . strtolower($val) . '%';

        if ($request->filled('nama'))         $query->whereRaw('LOWER(name) LIKE ?', [$like($request->nama)]);
        if ($request->filled('plat'))         $query->whereRaw('LOWER(plate_number) LIKE ?', [$like($request->plat)]);
        if ($request->filled('merk'))         $query->whereRaw('LOWER(CONCAT(COALESCE(brand,\'\'), \' \', COALESCE(model,\'\'))) LIKE ?', [$like($request->merk)]);
        if ($request->filled('type'))         $query->where('type', $request->type);
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

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:umum,ambulance'],
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.vehicles.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        Vehicle::create($data);

        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle)
    {
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:umum,ambulance'],
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles')->ignore($vehicle->id)],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.vehicles.edit', $vehicle)
                ->withErrors($validator)
                ->withInput();
        }

        $vehicle->update($validator->validated());

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
