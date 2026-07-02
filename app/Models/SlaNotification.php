<?php

namespace App\Models;

use App\Enums\SlaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaNotification extends Model
{
    protected $fillable = [
        'service_case_id',
        'recipient_id',
        'sla_status',
        'title',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'sla_status' => SlaStatus::class,
            'read_at' => 'datetime',
        ];
    }

    public function serviceCase(): BelongsTo
    {
        return $this->belongsTo(ServiceCase::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
