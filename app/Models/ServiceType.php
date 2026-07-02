<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $fillable = ['name', 'sla_hours', 'required_documents', 'is_active'];

    protected function casts(): array
    {
        return [
            'required_documents' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function serviceCases(): HasMany
    {
        return $this->hasMany(ServiceCase::class);
    }
}
