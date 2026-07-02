<?php

namespace App\Services;

use App\Enums\TransactionCorrectionStatus;
use App\Enums\TransactionStatus;
use App\Models\AdministrativeTransaction;
use App\Models\JournalEntry;
use App\Models\TransactionCorrectionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionCorrectionService
{
    public function __construct(
        private readonly ReferenceNumberService $references,
        private readonly TransactionAccountResolver $accounts,
    ) {}

    public function approve(TransactionCorrectionRequest $correction, User $checker, ?string $note = null): TransactionCorrectionRequest
    {
        return DB::transaction(function () use ($correction, $checker, $note): TransactionCorrectionRequest {
            $correction = TransactionCorrectionRequest::query()
                ->lockForUpdate()
                ->findOrFail($correction->id);
            $transaction = AdministrativeTransaction::query()
                ->lockForUpdate()
                ->findOrFail($correction->administrative_transaction_id);

            if ($correction->status !== TransactionCorrectionStatus::MenungguVerifikasi) {
                throw ValidationException::withMessages(['correction' => 'Permintaan koreksi sudah diproses.']);
            }

            if ($transaction->status !== TransactionStatus::Disetujui) {
                throw ValidationException::withMessages(['correction' => 'Hanya transaksi yang telah disetujui dapat dikoreksi.']);
            }

            if ($correction->requested_by === $checker->id) {
                throw ValidationException::withMessages(['correction' => 'Maker tidak dapat menyetujui koreksi transaksinya sendiri.']);
            }

            $reversal = JournalEntry::create([
                'journal_number' => 'PENDING',
                'administrative_transaction_id' => $transaction->id,
                'debit_account' => $transaction->credit_account,
                'credit_account' => $transaction->debit_account,
                'amount' => $transaction->amount,
                'entry_type' => 'reversal',
                'posted_by' => $checker->id,
                'posted_at' => now(),
            ]);
            $reversal->update(['journal_number' => $this->references->journalNumber($reversal)]);

            $mappedAccounts = $this->accounts->resolve($correction->proposed_category);
            $replacement = AdministrativeTransaction::create([
                'transaction_number' => 'PENDING',
                'service_case_id' => $transaction->service_case_id,
                'customer_id' => $transaction->customer_id,
                'created_by' => $correction->requested_by,
                'corrected_from_id' => $transaction->id,
                'category' => $correction->proposed_category,
                'payment_method' => $correction->proposed_payment_method,
                'amount' => $correction->proposed_amount,
                'debit_account' => $mappedAccounts['debit_account'],
                'credit_account' => $mappedAccounts['credit_account'],
                'description' => $correction->proposed_description,
                'proof_path' => $correction->supporting_path,
                'status' => TransactionStatus::Draft,
            ]);
            $replacement->update(['transaction_number' => $this->references->transactionNumber($replacement)]);

            $transaction->update([
                'status' => TransactionStatus::Dikoreksi,
                'cancelled_at' => now(),
            ]);

            $correction->update([
                'status' => TransactionCorrectionStatus::Disetujui,
                'reviewed_by' => $checker->id,
                'reviewed_at' => now(),
                'review_note' => $note,
                'replacement_transaction_id' => $replacement->id,
            ]);

            return $correction->fresh(['transaction', 'requestedBy', 'reviewedBy', 'replacementTransaction']);
        });
    }

    public function reject(TransactionCorrectionRequest $correction, User $checker, string $note): TransactionCorrectionRequest
    {
        return DB::transaction(function () use ($correction, $checker, $note): TransactionCorrectionRequest {
            $correction = TransactionCorrectionRequest::query()->lockForUpdate()->findOrFail($correction->id);

            if ($correction->status !== TransactionCorrectionStatus::MenungguVerifikasi) {
                throw ValidationException::withMessages(['correction' => 'Permintaan koreksi sudah diproses.']);
            }

            if ($correction->requested_by === $checker->id) {
                throw ValidationException::withMessages(['correction' => 'Maker tidak dapat menolak permintaan koreksinya sendiri.']);
            }

            $correction->update([
                'status' => TransactionCorrectionStatus::Ditolak,
                'reviewed_by' => $checker->id,
                'reviewed_at' => now(),
                'review_note' => $note,
            ]);

            return $correction->fresh(['transaction', 'requestedBy', 'reviewedBy']);
        });
    }
}
