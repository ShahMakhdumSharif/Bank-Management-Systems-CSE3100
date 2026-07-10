<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ATMLoginRequest extends FormRequest
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
            'card_number' => ['required', 'digits:16'],
            'pin' => ['required', 'digits:4'],
        ];
    }
}
