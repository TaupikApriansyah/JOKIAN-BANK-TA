<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Models\ServiceCase;
use App\Services\SlaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlaMonitoringController extends Controller
{
    public function index(Request $request, SlaService $sla): View
    {
        $sla->refreshOpenCases();
        $query = ServiceCase::query()
            ->with(['customer', 'serviceType', 'assignedTo'])
            ->whereNotIn('status', [CaseStatus::Selesai->value, CaseStatus::Ditolak->value])
            ->orderBy('due_at');

        if (!$request->user()->isAdmin()) {
            $query->where('assigned_to', $request->user()->id);
        }

        if ($request->filled('sla_status')) {
            $query->where('sla_status', $request->string('sla_status')->value());
        }

        return view('sla.index', ['cases' => $query->paginate(20)->withQueryString()]);
    }
}
