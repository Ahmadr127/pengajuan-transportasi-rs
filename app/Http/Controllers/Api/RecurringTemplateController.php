<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Admin\UpdateRecurringTemplateRequest;
use App\Models\RecurringTransportTemplate;
use Illuminate\Http\Request;

class RecurringTemplateController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = RecurringTransportTemplate::query()->with('user')->orderBy('id', 'desc');
        if ($request->filled('search')) {
            $s = '%'.strtolower($request->search).'%';
            $query->whereRaw('LOWER(pemohon_nama) LIKE ?', [$s]);
        }
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $perPage = min((int) $request->get('per_page', 15), 100);
        $templates = $query->paginate($perPage);
        return $this->paginated($templates, \App\Http\Resources\RecurringTemplateResource::collection($templates));
    }

    public function show(RecurringTransportTemplate $recurring_template)
    {
        return $this->success(new \App\Http\Resources\RecurringTemplateResource($recurring_template->load('user')));
    }

    public function update(UpdateRecurringTemplateRequest $request, RecurringTransportTemplate $recurring_template)
    {
        $recurring_template->update($request->validated());
        return $this->success(new \App\Http\Resources\RecurringTemplateResource($recurring_template->fresh()->load('user')), 'Template berhasil diupdate.');
    }

    public function destroy(RecurringTransportTemplate $recurring_template)
    {
        $recurring_template->delete();
        return $this->success(null, 'Template berhasil dihapus.');
    }
}