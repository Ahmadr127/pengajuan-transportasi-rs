<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringTransportTemplate;
use Illuminate\Http\Request;

class RecurringTransportTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = RecurringTransportTemplate::with('user')->latest();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.recurring-templates.index', compact('items'));
    }

    public function edit(RecurringTransportTemplate $recurring_template)
    {
        return view('admin.recurring-templates.edit', compact('recurring_template'));
    }

    public function update(Request $request, RecurringTransportTemplate $recurring_template)
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
            'hari' => ['required', 'array', 'min:1'],
            'hari.*' => ['integer', 'min:1', 'max:7'],
            'jam' => ['required', 'date_format:H:i'],
            'jam_sampai' => ['nullable', 'date_format:H:i'],
        ]);

        $recurring_template->update([
            'is_active' => $request->boolean('is_active'),
            'hari' => $request->hari,
            'jam' => $request->jam,
            'jam_sampai' => $request->boolean('sampai_selesai') ? null : ($request->jam_sampai ?: null),
        ]);

        return redirect()->route('admin.recurring-templates.index')
            ->with('success', 'Template pengajuan berulang berhasil diperbarui.');
    }

    public function destroy(RecurringTransportTemplate $recurring_template)
    {
        $recurring_template->delete();

        return redirect()->route('admin.recurring-templates.index')
            ->with('success', 'Template pengajuan berulang berhasil dihapus.');
    }
}
