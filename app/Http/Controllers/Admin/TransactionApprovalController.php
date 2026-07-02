<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyTransactionRequest;
use App\Models\AdministrativeTransaction;
use App\Services\AuditLogger;
use App\Services\TransactionApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionApprovalController extends Controller
{
    public function index(): View
    {
        return view('admin.transactions.index', [
            'transactions' => AdministrativeTransaction::query()
                ->where('status', TransactionStatus::MenungguVerifikasi->value)
                ->with(['customer', 'serviceCase', 'createdBy'])
                ->latest('submitted_at')
                ->paginate(15),
        ]);
    }

    public function show(AdministrativeTransaction $transaction): View
    {
        $transaction->load(['customer', 'serviceCase.serviceType', 'createdBy', 'verifiedBy', 'journals']);
        return view('admin.transactions.show', compact('transaction'));
    }

    public function verify(
        VerifyTransactionRequest $request,
        AdministrativeTransaction $transaction,
        TransactionApprovalService $approval,
        AuditLogger $audit,
    ): RedirectResponse {
        $before = ['status' => $transaction->status->value];

        if ($request->input('decision') === 'approve') {
            $transaction = $approval->approve($transaction, $request->user(), $request->input('note'));
            $audit->log($request, 'transaction', 'approve', $transaction, $before, ['status' => $transaction->status->value], 'Checker menyetujui transaksi dan jurnal terbentuk.');
            return redirect()->route('admin.transactions.show', $transaction)->with('success', 'Transaksi disetujui. Jurnal otomatis berhasil dibuat.');
        }

        $request->validate(['note' => ['required', 'string', 'max:500']]);
        $transaction = $approval->returnForCorrection($transaction, $request->user(), $request->input('note'));
        $audit->log($request, 'transaction', 'return_for_correction', $transaction, $before, ['status' => $transaction->status->value], 'Checker mengembalikan transaksi untuk perbaikan.');

        return redirect()->route('admin.transactions.index')->with('success', 'Transaksi dikembalikan kepada Maker untuk diperbaiki.');
    }
}
