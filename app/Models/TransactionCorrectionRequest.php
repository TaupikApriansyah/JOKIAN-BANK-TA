<?php

namespace App\Models;

use App\Enums\TransactionCorrectionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionCorrectionRequest extends Model
{
    protected $fillable = [
        'administrative_transaction_id',
        'requested_by',
        'reviewed_by',
        'replacement_transaction_id',
        'proposed_category',
        'proposed_payment_method',
        'proposed_amount',
        'proposed_description',
        'reason',
        'supporting_path',
        'status',
        'reviewed_at',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'proposed_amount' => 'decimal:2',
            'status' => TransactionCorrectionStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(AdministrativeTransaction::class, 'administrative_transaction_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function replacementTransaction(): BelongsTo
    {
        return $this->belongsTo(AdministrativeTransaction::class, 'replacement_transaction_id');
    }
}
