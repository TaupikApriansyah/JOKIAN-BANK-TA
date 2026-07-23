<?php

namespace App\Http\Controllers\Accountant;

use App\Enums\JournalStatus;
use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Services\AuditLogger;
use App\Services\JournalPostingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(array_column(JournalStatus::cases(), 'value'))],
            'entry_type' => ['nullable', Rule::in(['normal', 'reversal'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = JournalEntry::query()->with(['transaction.customer', 'transaction.serviceCase', 'preparedBy', 'postedBy']);

        $query->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status));
        $query->when($filters['entry_type'] ?? null, fn ($q, $type) => $q->where('entry_type', $type));
        $query->when($filters['date_from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date));
        $query->when($filters['date_to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
        $query->when($filters['search'] ?? null, function ($q, string $search): void {
            $q->where(function ($nested) use ($search): void {
                $nested->where('journal_number', 'like', "%{$search}%")
                    ->orWhere('debit_account', 'like', "%{$search}%")
                    ->orWhere('credit_account', 'like', "%{$search}%")
                    ->orWhereHas('transaction', fn ($trx) => $trx->where('transaction_number', 'like', "%{$search}%"))
                    ->orWhereHas('transaction.customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
            });
        });

        return view('accountant.journals.index', [
            'journals' => $query->latest()->paginate(15)->withQueryString(),
            'pendingCount' => JournalEntry::query()->where('status', JournalStatus::Draft->value)->count(),
            'postedCount' => JournalEntry::query()->where('status', JournalStatus::Posted->value)->count(),
        ]);
    }

    public function show(JournalEntry $journal): View
    {
        $journal->load(['transaction.customer', 'transaction.serviceCase.serviceType', 'transaction.createdBy', 'transaction.verifiedBy', 'preparedBy', 'postedBy']);

        return view('accountant.journals.show', compact('journal'));
    }

    public function post(
        Request $request,
        JournalEntry $journal,
        JournalPostingService $posting,
        AuditLogger $audit,
    ): RedirectResponse {
        $validated = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $before = [
            'status' => $journal->status->value,
            'posted_by' => $journal->posted_by,
            'posted_at' => $journal->posted_at?->toISOString(),
        ];

        $journal = $posting->post($journal, $request->user(), $validated['review_note'] ?? null);

        $audit->log($request, 'journal', 'post', $journal, $before, [
            'status' => $journal->status->value,
            'posted_by' => $journal->posted_by,
            'posted_at' => $journal->posted_at?->toISOString(),
        ], 'Akuntan memeriksa dan mem-posting jurnal.');

        return redirect()->route('accountant.journals.show', $journal)->with('success', 'Jurnal berhasil diposting.');
    }
}
