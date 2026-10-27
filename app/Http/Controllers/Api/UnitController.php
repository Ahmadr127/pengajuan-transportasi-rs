<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Unit::query()->orderBy('nama_unit');
        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(nama_unit) LIKE ? OR LOWER(kode_unit) LIKE ?', [$s, $s]);
        }
        if ($request->filled('status')) {
            $query->where('status_unit', $request->status);
        }
        $perPage = min((int) $request->get('per_page', 15), 100);
        $units = $query->paginate($perPage);
        return $this->paginated($units, \App\Http\Resources\UnitResource::collection($units));
    }

    public function store(StoreUnitRequest $request)
    {
        $unit = Unit::create($request->validated());
        return $this->created(new \App\Http\Resources\UnitResource($unit), 'Unit berhasil ditambahkan.');
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());
        return $this->success(new \App\Http\Resources\UnitResource($unit->fresh()), 'Unit berhasil diupdate.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->success(null, 'Unit berhasil dihapus.');
    }
}