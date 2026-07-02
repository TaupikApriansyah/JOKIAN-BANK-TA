<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdministrativeTransaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'service_case_id',
        'customer_id',
        'created_by',
        'verified_by',
        'corrected_from_id',
        'category',
        'payment_method',
        'amount',
        'debit_account',
        'credit_account',
        'description',
        'proof_path',
        'status',
        'submitted_at',
        'verified_at',
        'verification_note',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => TransactionStatus::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function serviceCase(): BelongsTo { return $this->belongsTo(ServiceCase::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function verifiedBy(): BelongsTo { return $this->belongsTo(User::class, 'verified_by'); }
    public function correctedFrom(): BelongsTo { return $this->belongsTo(self::class, 'corrected_from_id'); }
    public function corrections(): HasMany { return $this->hasMany(self::class, 'corrected_from_id'); }
    public function journals(): HasMany { return $this->hasMany(JournalEntry::class); }
    public function correctionRequests(): HasMany { return $this->hasMany(TransactionCorrectionRequest::class); }
}
