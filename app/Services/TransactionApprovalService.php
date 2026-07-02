<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\AdministrativeTransaction;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionApprovalService
{
    public function __construct(private readonly ReferenceNumberService $references) {}

    public function approve(AdministrativeTransaction $transaction, User $checker, ?string $note = null): AdministrativeTransaction
    {
        return DB::transaction(function () use ($transaction, $checker, $note) {
            $transaction = AdministrativeTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if ($transaction->status !== TransactionStatus::MenungguVerifikasi) {
                throw ValidationException::withMessages([
                    'transaction' => 'Transaksi tidak lagi tersedia untuk diverifikasi.',
                ]);
            }

            if ($transaction->created_by === $checker->id) {
                throw ValidationException::withMessages([
                    'transaction' => 'Maker tidak dapat menyetujui transaksinya sendiri.',
                ]);
            }

            $transaction->update([
                'status' => TransactionStatus::Disetujui,
                'verified_by' => $checker->id,
                'verified_at' => now(),
                'verification_note' => $note,
            ]);

            $entry = JournalEntry::create([
                'journal_number' => 'PENDING',
                'administrative_transaction_id' => $transaction->id,
                'debit_account' => $transaction->debit_account,
                'credit_account' => $transaction->credit_account,
                'amount' => $transaction->amount,
                'entry_type' => 'normal',
                'posted_by' => $checker->id,
                'posted_at' => now(),
            ]);

            $entry->update(['journal_number' => $this->references->journalNumber($entry)]);

            return $transaction->fresh(['journals', 'customer', 'serviceCase', 'createdBy', 'verifiedBy']);
        });
    }

    public function returnForCorrection(AdministrativeTransaction $transaction, User $checker, string $note): AdministrativeTransaction
    {
        return DB::transaction(function () use ($transaction, $checker, $note) {
            $transaction = AdministrativeTransaction::query()->lockForUpdate()->findOrFail($transaction->id);

            if ($transaction->status !== TransactionStatus::MenungguVerifikasi) {
                throw ValidationException::withMessages(['transaction' => 'Status transaksi sudah berubah.']);
            }

            $transaction->update([
                'status' => TransactionStatus::Dikembalikan,
                'verified_by' => $checker->id,
                'verified_at' => now(),
                'verification_note' => $note,
            ]);

            return $transaction->fresh();
        });
    }
}
