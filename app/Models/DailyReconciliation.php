<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReconciliation extends Model
{
    protected $fillable = [
        'reconciliation_date', 'system_total', 'physical_total', 'difference', 'status', 'note', 'created_by',
    ];

    protected function casts(): array
    {
        return ['reconciliation_date' => 'date', 'system_total' => 'decimal:2', 'physical_total' => 'decimal:2', 'difference' => 'decimal:2'];
    }

    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
