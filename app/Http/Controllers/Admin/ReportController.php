<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeTransaction;
use App\Models\AuditLog;
use App\Models\ExportLog;
use App\Models\JournalEntry;
use App\Models\ServiceCase;
use App\Services\AuditLogger;
use App\Services\SimplePdfExporter;
use App\Services\SpreadsheetExporter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'caseCount' => ServiceCase::count(),
            'transactionCount' => AdministrativeTransaction::count(),
            'journalCount' => JournalEntry::count(),
            'auditCount' => AuditLog::count(),
        ]);
    }

    public function downloadTransactionsExcel(Request $request, SpreadsheetExporter $exporter, AuditLogger $audit)
    {
        $transactions = $this->transactions();
        $path = $exporter->createXlsx($this->headers(), $this->rows($transactions), 'laporan-transaksi-'.now()->format('Ymd-His').'.xlsx');
        $this->recordExport($request, $audit, 'Excel (.xlsx)', 'export_xlsx', 'Laporan transaksi diunduh langsung dalam Excel (.xlsx).');
        return response()->download($path, 'laporan-transaksi-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadTransactionsPdf(Request $request, SimplePdfExporter $exporter, AuditLogger $audit): Response
    {
        $pdf = $exporter->createTablePdf('Laporan Transaksi Administrasi', $this->headers(), $this->rows($this->transactions()));
        $this->recordExport($request, $audit, 'PDF', 'export_pdf', 'Laporan transaksi diunduh langsung dalam PDF.');
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-transaksi-'.now()->format('Ymd-His').'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private function transactions()
    {
        return AdministrativeTransaction::query()->with(['customer', 'serviceCase', 'createdBy', 'verifiedBy'])->latest()->get();
    }
    /** @return array<int,string> */
    private function headers(): array
    {
        return ['No. Transaksi','No. Berkas','Nasabah','Kategori','Nominal','Status','Maker','Tanggal'];
    }
    /** @return array<int,array<int,string>> */
    private function rows($transactions): array
    {
        return $transactions->map(fn (AdministrativeTransaction $transaction) => [
            $transaction->transaction_number,
            $transaction->serviceCase->file_number,
            $transaction->customer->name,
            $transaction->category,
            'Rp '.number_format((float)$transaction->amount, 0, ',', '.'),
            $transaction->status->label(),
            $transaction->createdBy->name,
            $transaction->created_at->format('d/m/Y H:i'),
        ])->all();
    }
    private function recordExport(Request $request, AuditLogger $audit, string $format, string $action, string $description): void
    {
        ExportLog::create(['user_id'=>$request->user()->id,'report_type'=>'Laporan Transaksi Administrasi','format'=>$format,'ip_address'=>$request->ip(),'user_agent'=>$request->userAgent()]);
        $audit->log($request, 'report', $action, null, null, ['report'=>'transaction','format'=>$format], $description);
    }
}
