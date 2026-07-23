<?php

namespace App\Http\Controllers\Accountant;

use App\Enums\JournalStatus;
use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'account' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $accounts = JournalEntry::query()
            ->where('status', JournalStatus::Posted->value)
            ->get(['debit_account', 'credit_account'])
            ->flatMap(fn (JournalEntry $entry) => [$entry->debit_account, $entry->credit_account])
            ->unique()
            ->sort()
            ->values();

        $selectedAccount = $validated['account'] ?? $accounts->first();
        $entries = collect();
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        if ($selectedAccount) {
            $query = JournalEntry::query()
                ->where('status', JournalStatus::Posted->value)
                ->where(fn ($q) => $q->where('debit_account', $selectedAccount)->orWhere('credit_account', $selectedAccount))
                ->with(['transaction.customer', 'postedBy'])
                ->orderBy('posted_at')
                ->orderBy('id');

            $query->when($validated['date_from'] ?? null, fn ($q, $date) => $q->whereDate('posted_at', '>=', $date));
            $query->when($validated['date_to'] ?? null, fn ($q, $date) => $q->whereDate('posted_at', '<=', $date));

            $entries = $query->get();
            foreach ($entries as $entry) {
                if ($entry->debit_account === $selectedAccount) {
                    $totalDebit += (float) $entry->amount;
                }
                if ($entry->credit_account === $selectedAccount) {
                    $totalCredit += (float) $entry->amount;
                }
            }
        }

        return view('accountant.ledger.index', [
            'accounts' => $accounts,
            'selectedAccount' => $selectedAccount,
            'entries' => $entries,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $totalDebit - $totalCredit,
        ]);
    }
}
