<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountantUserSeeder extends Seeder
{
    public function run(): void
    {
        $accountant = User::query()
            ->where('employee_id', 'ACC-001')
            ->orWhere('email', 'akuntan@bankx.test')
            ->first();

        if ($accountant) {
            $accountant->update([
                'employee_id' => 'ACC-001',
                'name' => $accountant->name ?: 'Sari Akuntan',
                'email' => 'akuntan@bankx.test',
                'role' => 'accountant',
                'is_active' => true,
            ]);

            return;
        }

        User::query()->create([
            'employee_id' => 'ACC-001',
            'name' => 'Sari Akuntan',
            'email' => 'akuntan@bankx.test',
            'role' => 'accountant',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);
    }
}
