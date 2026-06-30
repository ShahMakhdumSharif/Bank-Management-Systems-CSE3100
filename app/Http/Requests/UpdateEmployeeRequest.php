<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMasterAdmin() ?? false;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'employee_code' => [
                'required',
                'string',
                'max:40',
                Rule::unique('users', 'employee_code')->ignore($employee),
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['integer', 'exists:branches,id'],
            'branch_position' => ['nullable', 'string', 'max:120'],
        ];
    }
}
