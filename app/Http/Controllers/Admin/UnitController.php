<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUnitRequest;
use App\Http\Requests\Admin\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query()->orderBy('nama_unit');
        $like = fn ($val) => '%'.strtolower($val).'%';

        if ($request->filled('nama')) {
            $query->whereRaw('LOWER(nama_unit) LIKE ?', [$like($request->nama)]);
        }
        if ($request->filled('kode')) {
            $query->whereRaw('LOWER(kode_unit) LIKE ?', [$like($request->kode)]);
        }
        if ($request->filled('status')) {
            $query->where('status_unit', $request->status);
        }

        $units = $query->paginate(15)->withQueryString();

        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(StoreUnitRequest $request)
    {
        Unit::create($request->validated());

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil diupdate.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil dihapus.');
    }
}
