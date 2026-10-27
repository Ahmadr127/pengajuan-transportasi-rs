<?php

namespace App\Http\Controllers\Api;

use App\Services\LaporanExportService;
use App\Services\TransportRequestQueryService;
use Illuminate\Http\Request;

class ReportController extends BaseApiController
{
    public function __construct(
        private readonly TransportRequestQueryService $query,
        private readonly LaporanExportService $export,
    ) {}

    public function index(Request $request)
    {
        $items = $this->query->laporanQuery($request->all())->paginate(min((int) $request->get('per_page', 15), 100));
        return $this->paginated($items, \App\Http\Resources\TransportRequestResource::collection($items));
    }

    public function export(Request $request)
    {
        return $this->export->stream($request->all());
    }
}