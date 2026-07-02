<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseDocument extends Model
{
    protected $fillable = [
        'service_case_id', 'document_type', 'original_name', 'storage_path', 'mime_type',
        'size_bytes', 'uploaded_by', 'retention_until',
    ];

    protected function casts(): array
    {
        return ['retention_until' => 'date'];
    }

    public function serviceCase(): BelongsTo { return $this->belongsTo(ServiceCase::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
