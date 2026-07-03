<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FreezeAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployee() ?? false;
    }

    public function rules(): array
    {
        return [
            'freeze_reason' => ['required', 'string', 'min:8', 'max:500'],
        ];
    }
}
