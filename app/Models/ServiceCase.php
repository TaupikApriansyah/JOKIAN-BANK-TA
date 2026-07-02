<?php

namespace App\Models;

use App\Enums\CaseStatus;
use App\Enums\SlaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCase extends Model
{
    protected $fillable = [
        'file_number',
        'customer_id',
        'service_type_id',
        'assigned_to',
        'created_by',
        'status',
        'sla_status',
        'received_at',
        'due_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => CaseStatus::class,
            'sla_status' => SlaStatus::class,
            'received_at' => 'datetime',
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function serviceType(): BelongsTo { return $this->belongsTo(ServiceType::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function documents(): HasMany { return $this->hasMany(CaseDocument::class); }
    public function transactions(): HasMany { return $this->hasMany(AdministrativeTransaction::class); }
    public function slaNotifications(): HasMany { return $this->hasMany(SlaNotification::class); }

    public function hasAllRequiredDocuments(): bool
    {
        $required = $this->serviceType?->required_documents ?? [];

        if ($required === []) {
            return true;
        }

        $uploaded = $this->documents()->pluck('document_type')->all();

        return collect($required)->every(fn (string $document): bool => in_array($document, $uploaded, true));
    }

    /** @return array<int, string> */
    public function missingDocuments(): array
    {
        $required = $this->serviceType?->required_documents ?? [];
        $uploaded = $this->documents()->pluck('document_type')->all();

        return array_values(array_diff($required, $uploaded));
    }
}
