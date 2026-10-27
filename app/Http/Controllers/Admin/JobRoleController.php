<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobRoleRequest;
use App\Http\Requests\Admin\UpdateJobRoleRequest;
use App\Models\JobRole;
use Illuminate\Http\Request;

class JobRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = JobRole::query()->orderBy('nama_role');
        $like = fn ($val) => '%'.strtolower($val).'%';

        if ($request->filled('nama')) {
            $query->whereRaw('LOWER(nama_role) LIKE ?', [$like($request->nama)]);
        }

        $jobRoles = $query->paginate(15)->withQueryString();

        return view('admin.job-roles.index', compact('jobRoles'));
    }

    public function create()
    {
        return view('admin.job-roles.create');
    }

    public function store(StoreJobRoleRequest $request)
    {
        JobRole::create($request->validated());

        return redirect()->route('admin.job-roles.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(JobRole $job_role)
    {
        return view('admin.job-roles.edit', compact('job_role'));
    }

    public function update(UpdateJobRoleRequest $request, JobRole $job_role)
    {
        $job_role->update($request->validated());

        return redirect()->route('admin.job-roles.index')
            ->with('success', 'Jabatan berhasil diupdate.');
    }

    public function destroy(JobRole $job_role)
    {
        $job_role->delete();

        return redirect()->route('admin.job-roles.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
