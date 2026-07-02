<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomerService() ?? false;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(array_keys(config('bank.transaction_categories', [])))],
            'payment_method' => ['required', Rule::in(['Setoran Tunai', 'Potong Saldo Rekening (Auto-debit)'])],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999'],
            'description' => ['nullable', 'string', 'max:500'],
            'proof' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'action' => ['required', Rule::in(['draft', 'submit'])],
        ];
    }
}
