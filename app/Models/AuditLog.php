<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'role', 'module', 'action', 'subject_type', 'subject_id',
        'before_values', 'after_values', 'ip_address', 'user_agent', 'description',
    ];

    protected function casts(): array
    {
        return ['before_values' => 'array', 'after_values' => 'array'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
