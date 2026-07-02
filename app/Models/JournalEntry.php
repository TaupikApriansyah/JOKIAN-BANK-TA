<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_number', 'administrative_transaction_id', 'debit_account', 'credit_account',
        'amount', 'entry_type', 'posted_by', 'posted_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'posted_at' => 'datetime'];
    }

    public function transaction(): BelongsTo { return $this->belongsTo(AdministrativeTransaction::class, 'administrative_transaction_id'); }
    public function postedBy(): BelongsTo { return $this->belongsTo(User::class, 'posted_by'); }
}
