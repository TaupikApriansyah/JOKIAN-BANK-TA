<?php

namespace App\Services;

use App\Enums\JournalStatus;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalPostingService
{
    public function post(JournalEntry $journal, User $accountant, ?string $note = null): JournalEntry
    {
        return DB::transaction(function () use ($journal, $accountant, $note): JournalEntry {
            $journal = JournalEntry::query()->lockForUpdate()->findOrFail($journal->id);

            if (! $accountant->isAccountant()) {
                throw ValidationException::withMessages([
                    'journal' => 'Hanya Akuntan yang dapat melakukan posting jurnal.',
                ]);
            }

            if ($journal->status !== JournalStatus::Draft) {
                throw ValidationException::withMessages([
                    'journal' => 'Jurnal ini sudah diposting atau tidak lagi tersedia.',
                ]);
            }

            if ((float) $journal->amount <= 0) {
                throw ValidationException::withMessages([
                    'journal' => 'Nominal jurnal harus lebih besar dari nol.',
                ]);
            }

            if (trim($journal->debit_account) === trim($journal->credit_account)) {
                throw ValidationException::withMessages([
                    'journal' => 'Akun debit dan kredit tidak boleh sama.',
                ]);
            }

            $transaction = $journal->transaction()->firstOrFail();

            if ($journal->entry_type === 'reversal') {
                $postedOriginal = JournalEntry::query()
                    ->where('administrative_transaction_id', $journal->administrative_transaction_id)
                    ->where('entry_type', 'normal')
                    ->where('status', JournalStatus::Posted->value)
                    ->lockForUpdate()
                    ->first();

                if (! $postedOriginal) {
                    throw ValidationException::withMessages([
                        'journal' => 'Jurnal pembalik hanya dapat diposting setelah jurnal transaksi asal diposting.',
                    ]);
                }
            }

            if ($journal->entry_type === 'normal' && $transaction->corrected_from_id) {
                $postedReversal = JournalEntry::query()
                    ->where('administrative_transaction_id', $transaction->corrected_from_id)
                    ->where('entry_type', 'reversal')
                    ->where('status', JournalStatus::Posted->value)
                    ->lockForUpdate()
                    ->first();

                if (! $postedReversal) {
                    throw ValidationException::withMessages([
                        'journal' => 'Jurnal transaksi pengganti hanya dapat diposting setelah jurnal pembalik transaksi asal diposting.',
                    ]);
                }
            }

            $journal->update([
                'status' => JournalStatus::Posted,
                'posted_by' => $accountant->id,
                'posted_at' => now(),
                'review_note' => $note,
            ]);

            return $journal->fresh([
                'transaction.customer',
                'transaction.serviceCase',
                'preparedBy',
                'postedBy',
            ]);
        });
    }
}
