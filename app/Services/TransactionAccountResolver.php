<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class TransactionAccountResolver
{
    /**
     * Returns the configured accounting pair for an approved transaction category.
     * Account mapping is centralised in config/bank.php to avoid controller-level magic values.
     *
     * @return array{debit_account: string, credit_account: string}
     */
    public function resolve(string $category): array
    {
        $categories = config('bank.transaction_categories', []);
        $accounts = $categories[$category] ?? null;

        if ($accounts === null) {
            throw ValidationException::withMessages([
                'category' => 'Kategori transaksi tidak memiliki pemetaan akun jurnal.',
            ]);
        }

        return $accounts;
    }
}
