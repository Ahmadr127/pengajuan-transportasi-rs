<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreDriverRequest;
use App\Http\Requests\Admin\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Driver::query()->with('user')->orderBy('name');
        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(name) LIKE ? OR LOWER(phone) LIKE ? OR LOWER(license_number) LIKE ?', [$s, $s, $s]);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $perPage = min((int) $request->get('per_page', 15), 100);
        $drivers = $query->paginate($perPage);
        return $this->paginated($drivers, \App\Http\Resources\DriverResource::collection($drivers));
    }

    public function show(Driver $driver)
    {
        return $this->success(new \App\Http\Resources\DriverResource($driver->load('user')));
    }

    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->validated());
        return $this->created(new \App\Http\Resources\DriverResource($driver->load('user')), 'Driver berhasil ditambahkan.');
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->validated());
        return $this->success(new \App\Http\Resources\DriverResource($driver->fresh()->load('user')), 'Driver berhasil diupdate.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return $this->success(null, 'Driver berhasil dihapus.');
    }
}