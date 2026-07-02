<?php

namespace App\Enums;

enum UserRole: string
{
    case CustomerService = 'cs';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::CustomerService => 'Customer Service / Maker',
            self::Admin => 'Admin Supervisor / Checker',
        };
    }
}
