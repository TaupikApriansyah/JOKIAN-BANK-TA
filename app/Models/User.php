<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'role',
        'is_active',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'role' => UserRole::class,
            'password' => 'hashed',
        ];
    }

    public function assignedCustomers(): HasMany { return $this->hasMany(Customer::class, 'assigned_to'); }
    public function assignedCases(): HasMany { return $this->hasMany(ServiceCase::class, 'assigned_to'); }
    public function slaNotifications(): HasMany { return $this->hasMany(SlaNotification::class, 'recipient_id'); }

    public function isAdmin(): bool { return $this->role === UserRole::Admin; }
    public function isCustomerService(): bool { return $this->role === UserRole::CustomerService; }
}
