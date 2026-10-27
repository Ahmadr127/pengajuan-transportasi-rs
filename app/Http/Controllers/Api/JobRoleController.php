<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\StoreJobRoleRequest;
use App\Http\Requests\Admin\UpdateJobRoleRequest;
use App\Models\JobRole;
use Illuminate\Http\Request;

class JobRoleController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = JobRole::query()->orderBy('nama_role');
        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(nama_role) LIKE ?', [$s]);
        }
        $perPage = min((int) $request->get('per_page', 15), 100);
        $roles = $query->paginate($perPage);
        return $this->paginated($roles, \App\Http\Resources\JobRoleResource::collection($roles));
    }

    public function store(StoreJobRoleRequest $request)
    {
        $role = JobRole::create($request->validated());
        return $this->created(new \App\Http\Resources\JobRoleResource($role), 'Jabatan berhasil ditambahkan.');
    }

    public function update(UpdateJobRoleRequest $request, JobRole $job_role)
    {
        $job_role->update($request->validated());
        return $this->success(new \App\Http\Resources\JobRoleResource($job_role->fresh()), 'Jabatan berhasil diupdate.');
    }

    public function destroy(JobRole $job_role)
    {
        $job_role->delete();
        return $this->success(null, 'Jabatan berhasil dihapus.');
    }
}