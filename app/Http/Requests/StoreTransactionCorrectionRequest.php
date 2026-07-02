<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomerService() ?? false;
    }

    public function rules(): array
    {
        return [
            'proposed_category' => ['required', Rule::in(array_keys(config('bank.transaction_categories', [])))],
            'proposed_payment_method' => ['required', Rule::in(['Setoran Tunai', 'Potong Saldo Rekening (Auto-debit)'])],
            'proposed_amount' => ['required', 'numeric', 'min:1', 'max:999999999'],
            'proposed_description' => ['nullable', 'string', 'max:500'],
            'reason' => ['required', 'string', 'min:10', 'max:500'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
