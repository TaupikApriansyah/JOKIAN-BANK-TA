<?php

namespace App\Http\Controllers;

use App\Enums\TransactionCorrectionStatus;
use App\Enums\TransactionStatus;
use App\Http\Requests\StoreTransactionCorrectionRequest;
use App\Http\Requests\VerifyCorrectionRequest;
use App\Models\AdministrativeTransaction;
use App\Models\TransactionCorrectionRequest;
use App\Services\AuditLogger;
use App\Services\TransactionCorrectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionCorrectionController extends Controller
{
    public function create(Request $request, AdministrativeTransaction $transaction): View
    {
        $this->authorizeMaker($request, $transaction);
        abort_unless($transaction->status === TransactionStatus::Disetujui, 422, 'Hanya transaksi yang disetujui dapat diajukan koreksi.');

        return view('transactions.correction-create', compact('transaction'));
    }

    public function store(
        StoreTransactionCorrectionRequest $request,
        AdministrativeTransaction $transaction,
        AuditLogger $audit,
    ): RedirectResponse {
        $this->authorizeMaker($request, $transaction);
        abort_unless($transaction->status === TransactionStatus::Disetujui, 422, 'Hanya transaksi yang disetujui dapat diajukan koreksi.');
        abort_if(
            $transaction->correctionRequests()->where('status', TransactionCorrectionStatus::MenungguVerifikasi->value)->exists(),
            422,
            'Masih ada permintaan koreksi yang menunggu verifikasi.',
        );

        $supportingPath = $request->hasFile('supporting_document')
            ? $request->file('supporting_document')->store("private/transaction-corrections/{$transaction->transaction_number}", 'local')
            : null;

        $correction = TransactionCorrectionRequest::create([
            'administrative_transaction_id' => $transaction->id,
            'requested_by' => $request->user()->id,
            'proposed_category' => $request->string('proposed_category')->value(),
            'proposed_payment_method' => $request->string('proposed_payment_method')->value(),
            'proposed_amount' => $request->input('proposed_amount'),
            'proposed_description' => $request->input('proposed_description'),
            'reason' => $request->string('reason')->trim(),
            'supporting_path' => $supportingPath,
            'status' => TransactionCorrectionStatus::MenungguVerifikasi,
        ]);

        $audit->log($request, 'transaction_correction', 'submit', $correction, null, [
            'transaction_number' => $transaction->transaction_number,
            'proposed_amount' => $correction->proposed_amount,
            'status' => $correction->status->value,
        ], 'Maker mengajukan koreksi transaksi.');

        return redirect()->route('cases.show', $transaction->serviceCase)->with('success', 'Permintaan koreksi berhasil diajukan ke Admin.');
    }

    public function index(): View
    {
        return view('admin.corrections.index', [
            'corrections' => TransactionCorrectionRequest::query()
                ->where('status', TransactionCorrectionStatus::MenungguVerifikasi->value)
                ->with(['transaction.customer', 'requestedBy'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(TransactionCorrectionRequest $correction): View
    {
        $correction->load(['transaction.customer', 'transaction.serviceCase', 'requestedBy', 'reviewedBy', 'replacementTransaction']);

        return view('admin.corrections.show', compact('correction'));
    }

    public function verify(
        VerifyCorrectionRequest $request,
        TransactionCorrectionRequest $correction,
        TransactionCorrectionService $service,
        AuditLogger $audit,
    ): RedirectResponse {
        $before = ['status' => $correction->status->value];

        if ($request->input('decision') === 'approve') {
            $correction = $service->approve($correction, $request->user(), $request->input('note'));
            $audit->log($request, 'transaction_correction', 'approve', $correction, $before, [
                'status' => $correction->status->value,
                'replacement_transaction_id' => $correction->replacement_transaction_id,
            ], 'Checker menyetujui koreksi, membuat jurnal pembalik, dan membuat transaksi pengganti draft.');

            return redirect()->route('admin.corrections.show', $correction)->with('success', 'Koreksi disetujui. Jurnal pembalik dan draft transaksi pengganti berhasil dibuat.');
        }

        $request->validate(['note' => ['required', 'string', 'max:500']]);
        $correction = $service->reject($correction, $request->user(), $request->input('note'));
        $audit->log($request, 'transaction_correction', 'reject', $correction, $before, ['status' => $correction->status->value], 'Checker menolak permintaan koreksi transaksi.');

        return redirect()->route('admin.corrections.index')->with('success', 'Permintaan koreksi ditolak.');
    }

    private function authorizeMaker(Request $request, AdministrativeTransaction $transaction): void
    {
        abort_unless($request->user()->isCustomerService() && $transaction->created_by === $request->user()->id, 403);
    }
}
