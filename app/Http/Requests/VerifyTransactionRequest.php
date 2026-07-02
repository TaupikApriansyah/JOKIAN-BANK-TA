<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyTransactionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->isAdmin() ?? false; }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['approve', 'return'])],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
