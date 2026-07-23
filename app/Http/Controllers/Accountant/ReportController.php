<?php

namespace App\Http\Controllers\Accountant;

use App\Enums\JournalStatus;
use App\Http\Controllers\Controller;
use App\Models\AdministrativeTransaction;
use App\Models\DailyReconciliation;
use App\Models\ExportLog;
use App\Models\JournalEntry;
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
        return view('accountant.reports.index', [
            'transactionCount' => AdministrativeTransaction::count(),
            'draftJournalCount' => JournalEntry::query()->where('status', JournalStatus::Draft->value)->count(),
            'postedJournalCount' => JournalEntry::query()->where('status', JournalStatus::Posted->value)->count(),
            'reconciliationCount' => DailyReconciliation::count(),
        ]);
    }

    public function downloadTransactionsExcel(Request $request, SpreadsheetExporter $exporter, AuditLogger $audit)
    {
        $path = $exporter->createXlsx(
            $this->transactionHeaders(),
            $this->transactionRows($this->transactions()),
            'laporan-transaksi-'.now()->format('Ymd-His').'.xlsx',
            'Transaksi',
            'Laporan Transaksi Administrasi',
        );
        $this->recordExport($request, $audit, 'Laporan Transaksi Administrasi', 'Excel (.xlsx)', 'export_transactions_xlsx');

        return response()->download($path, 'laporan-transaksi-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadTransactionsPdf(Request $request, SimplePdfExporter $exporter, AuditLogger $audit): Response
    {
        $pdf = $exporter->createTablePdf(
            'Laporan Transaksi Administrasi',
            $this->transactionHeaders(),
            $this->transactionRows($this->transactions()),
        );
        $this->recordExport($request, $audit, 'Laporan Transaksi Administrasi', 'PDF', 'export_transactions_pdf');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-transaksi-'.now()->format('Ymd-His').'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function downloadJournalsExcel(Request $request, SpreadsheetExporter $exporter, AuditLogger $audit)
    {
        $path = $exporter->createXlsx(
            $this->journalHeaders(),
            $this->journalRows($this->journals()),
            'laporan-jurnal-'.now()->format('Ymd-His').'.xlsx',
            'Jurnal',
            'Laporan Jurnal Akuntansi',
        );
        $this->recordExport($request, $audit, 'Laporan Jurnal Akuntansi', 'Excel (.xlsx)', 'export_journals_xlsx');

        return response()->download($path, 'laporan-jurnal-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function downloadJournalsPdf(Request $request, SimplePdfExporter $exporter, AuditLogger $audit): Response
    {
        $pdf = $exporter->createTablePdf(
            'Laporan Jurnal Akuntansi',
            $this->journalHeaders(),
            $this->journalRows($this->journals()),
        );
        $this->recordExport($request, $audit, 'Laporan Jurnal Akuntansi', 'PDF', 'export_journals_pdf');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-jurnal-'.now()->format('Ymd-His').'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private function transactions()
    {
        return AdministrativeTransaction::query()
            ->with(['customer', 'serviceCase', 'createdBy', 'verifiedBy'])
            ->latest()
            ->get();
    }

    private function journals()
    {
        return JournalEntry::query()
            ->with(['transaction.customer', 'preparedBy', 'postedBy'])
            ->latest()
            ->get();
    }

    /** @return array<int, string> */
    private function transactionHeaders(): array
    {
        return ['No. Transaksi', 'No. Berkas', 'Nasabah', 'Kategori', 'Nominal', 'Status', 'Maker', 'Checker'];
    }

    /** @return array<int, array<int, string>> */
    private function transactionRows($transactions): array
    {
        return $transactions->map(fn (AdministrativeTransaction $transaction) => [
            $transaction->transaction_number,
            $transaction->serviceCase->file_number,
            $transaction->customer->name,
            $transaction->category,
            'Rp '.number_format((float) $transaction->amount, 0, ',', '.'),
            $transaction->status->label(),
            $transaction->createdBy->name,
            $transaction->verifiedBy?->name ?? '-',
        ])->all();
    }

    /** @return array<int, string> */
    private function journalHeaders(): array
    {
        return ['No. Jurnal', 'No. Transaksi', 'Nasabah', 'Debit', 'Kredit', 'Nominal', 'Status', 'Akuntan'];
    }

    /** @return array<int, array<int, string>> */
    private function journalRows($journals): array
    {
        return $journals->map(fn (JournalEntry $journal) => [
            $journal->journal_number,
            $journal->transaction->transaction_number,
            $journal->transaction->customer->name,
            $journal->debit_account,
            $journal->credit_account,
            'Rp '.number_format((float) $journal->amount, 0, ',', '.'),
            $journal->status->label(),
            $journal->postedBy?->name ?? '-',
        ])->all();
    }

    private function recordExport(Request $request, AuditLogger $audit, string $reportType, string $format, string $action): void
    {
        ExportLog::create([
            'user_id' => $request->user()->id,
            'report_type' => $reportType,
            'format' => $format,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $audit->log($request, 'accounting_report', $action, null, null, [
            'report_type' => $reportType,
            'format' => $format,
        ], "Akuntan mengunduh {$reportType} dalam format {$format}.");
    }
}
