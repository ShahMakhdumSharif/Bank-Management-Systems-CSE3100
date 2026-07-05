<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
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
            'receiver_account_number' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'receiver_account_number' => 'receiver account number',
        ];
    }
}
