<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ATMTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:1', 'max:999999999.99'],
        ];
    }
}
