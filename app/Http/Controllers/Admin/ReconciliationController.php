<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReconciliationRequest;
use App\Models\AdministrativeTransaction;
use App\Models\DailyReconciliation;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReconciliationController extends Controller
{
    public function index(Request $request): View
    {
        $date = Carbon::parse($request->input('date', today()->toDateString()))->toDateString();

        return view('admin.reconciliations.index', [
            'date' => $date,
            'systemTotal' => $this->systemTotal($date),
            'reconciliation' => DailyReconciliation::query()->whereDate('reconciliation_date', $date)->first(),
            'recentReconciliations' => DailyReconciliation::query()->with('createdBy')->latest('reconciliation_date')->take(10)->get(),
        ]);
    }

    public function store(StoreReconciliationRequest $request, AuditLogger $audit): RedirectResponse
    {
        $date = Carbon::parse($request->input('reconciliation_date'))->toDateString();
        $systemTotal = $this->systemTotal($date);
        $physicalTotal = (float) $request->input('physical_total');
        $difference = round($physicalTotal - (float) $systemTotal, 2);
        $status = match (true) {
            $difference === 0.0 => 'sesuai',
            $difference > 0 => 'selisih_lebih',
            default => 'selisih_kurang',
        };

        $reconciliation = DB::transaction(function () use ($request, $date, $systemTotal, $physicalTotal, $difference, $status): DailyReconciliation {
            $record = DailyReconciliation::query()->lockForUpdate()->whereDate('reconciliation_date', $date)->first();

            return DailyReconciliation::query()->updateOrCreate(
                ['reconciliation_date' => $date],
                [
                    'system_total' => $systemTotal,
                    'physical_total' => $physicalTotal,
                    'difference' => $difference,
                    'status' => $status,
                    'note' => $request->input('note'),
                    'created_by' => $request->user()->id,
                ],
            );
        });

        $audit->log($request, 'reconciliation', 'save', $reconciliation, null, [
            'reconciliation_date' => $date,
            'system_total' => $systemTotal,
            'physical_total' => $physicalTotal,
            'difference' => $difference,
            'status' => $status,
        ], 'Rekonsiliasi kas harian disimpan oleh Checker.');

        return redirect()->route('admin.reconciliations.index', ['date' => $date])->with('success', 'Rekonsiliasi harian berhasil disimpan.');
    }

    private function systemTotal(string $date): string
    {
        return (string) AdministrativeTransaction::query()
            ->where('status', TransactionStatus::Disetujui->value)
            ->whereDate('verified_at', $date)
            ->sum('amount');
    }
}
