<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Enums\SlaStatus;
use App\Enums\TransactionStatus;
use App\Models\AdministrativeTransaction;
use App\Models\AuditLog;
use App\Models\DailyReconciliation;
use App\Models\ServiceCase;
use App\Services\SlaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, SlaService $sla): View
    {
        $sla->refreshOpenCases();
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'activeCases' => ServiceCase::whereNotIn('status', [CaseStatus::Selesai->value, CaseStatus::Ditolak->value])->count(),
                'overdueCases' => ServiceCase::where('sla_status', SlaStatus::Terlambat->value)->with(['customer', 'assignedTo'])->latest('due_at')->take(5)->get(),
                'pendingTransactions' => AdministrativeTransaction::where('status', TransactionStatus::MenungguVerifikasi->value)->with(['customer', 'createdBy', 'serviceCase'])->latest('submitted_at')->take(5)->get(),
                'pendingCount' => AdministrativeTransaction::where('status', TransactionStatus::MenungguVerifikasi->value)->count(),
                'reconciliation' => DailyReconciliation::whereDate('reconciliation_date', today())->first(),
                'recentAudits' => AuditLog::with('user')->latest()->take(7)->get(),
            ]);
        }

        $baseCases = ServiceCase::query()->where('assigned_to', $user->id);

        return view('dashboard.cs', [
            'counts' => [
                'active' => (clone $baseCases)->where('status', CaseStatus::Diproses->value)->count(),
                'waitingDocuments' => (clone $baseCases)->where('status', CaseStatus::MenungguDokumen->value)->count(),
                'nearSla' => (clone $baseCases)->where('sla_status', SlaStatus::Mendekati->value)->count(),
                'overdue' => (clone $baseCases)->where('sla_status', SlaStatus::Terlambat->value)->count(),
                'pendingTransactions' => AdministrativeTransaction::where('created_by', $user->id)->where('status', TransactionStatus::MenungguVerifikasi->value)->count(),
            ],
            'priorityCases' => (clone $baseCases)->with(['customer', 'serviceType'])->whereNotIn('status', [CaseStatus::Selesai->value, CaseStatus::Ditolak->value])->orderBy('due_at')->take(8)->get(),
            'recentTransactions' => AdministrativeTransaction::where('created_by', $user->id)->with('serviceCase')->latest()->take(5)->get(),
        ]);
    }
}
