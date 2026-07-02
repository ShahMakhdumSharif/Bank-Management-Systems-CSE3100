<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMasterAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'branch_code' => ['required', 'string', 'max:30', 'unique:branches,branch_code'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:120'],
            'country_code' => ['required', 'string', 'size:2'],
            'is_active' => ['required', 'boolean'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => [
                'integer',
                Rule::exists('users', 'id')->where('role', User::ROLE_EMPLOYEE),
            ],
        ];
    }
}
