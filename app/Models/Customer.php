<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Customer extends Model
{
    protected $fillable = [
        'customer_number', 'name', 'nik', 'account_number', 'nik_encrypted', 'account_number_encrypted',
        'phone', 'email', 'assigned_to', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function serviceCases(): HasMany
    {
        return $this->hasMany(ServiceCase::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(AdministrativeTransaction::class);
    }

    public function setNikAttribute(?string $value): void
    {
        $this->attributes['nik_encrypted'] = filled($value) ? Crypt::encryptString($value) : null;
    }

    public function setAccountNumberAttribute(?string $value): void
    {
        $this->attributes['account_number_encrypted'] = filled($value) ? Crypt::encryptString($value) : null;
    }

    public function maskedNik(): string
    {
        return $this->mask($this->decryptSafely($this->nik_encrypted), 4, 4);
    }

    public function maskedAccountNumber(): string
    {
        return $this->mask($this->decryptSafely($this->account_number_encrypted), 4, 4);
    }

    private function decryptSafely(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function mask(?string $value, int $prefix, int $suffix): string
    {
        if (blank($value)) {
            return '-';
        }

        $length = strlen($value);
        if ($length <= ($prefix + $suffix)) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, $prefix)
            . str_repeat('*', max(4, $length - $prefix - $suffix))
            . substr($value, -$suffix);
    }
}
