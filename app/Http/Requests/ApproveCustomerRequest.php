<?php

namespace App\Http\Requests;

use App\Models\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApproveCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployee() ?? false;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', Rule::exists('branches', 'id')->where('is_active', true)],
            'account_type' => ['required', Rule::in([Account::TYPE_SAVINGS, Account::TYPE_CURRENT])],
        ];
    }
}
