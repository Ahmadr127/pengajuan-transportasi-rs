<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->orderBy('name');
        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(name) LIKE ? OR LOWER(plate_number) LIKE ?', [$s, $s]);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $perPage = min((int) $request->get('per_page', 15), 100);
        $vehicles = $query->paginate($perPage);
        return $this->paginated($vehicles, \App\Http\Resources\VehicleResource::collection($vehicles));
    }

    public function show(Vehicle $vehicle)
    {
        return $this->success(new \App\Http\Resources\VehicleResource($vehicle));
    }

    public function store(StoreVehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->validated());
        return $this->created(new \App\Http\Resources\VehicleResource($vehicle), 'Kendaraan berhasil ditambahkan.');
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());
        return $this->success(new \App\Http\Resources\VehicleResource($vehicle->fresh()), 'Kendaraan berhasil diupdate.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return $this->success(null, 'Kendaraan berhasil dihapus.');
    }
}