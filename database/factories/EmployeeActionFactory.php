<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\EmployeeAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeAction>
 */
class EmployeeActionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id' => User::factory()->state([
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_APPROVED,
            ]),
            'subject_user_id' => User::factory()->state([
                'role' => User::ROLE_CUSTOMER,
            ]),
            'branch_id' => Branch::factory(),
            'action_type' => fake()->randomElement([
                EmployeeAction::TYPE_CUSTOMER_APPROVED,
                EmployeeAction::TYPE_CUSTOMER_REJECTED,
                EmployeeAction::TYPE_ACCOUNT_FROZEN,
                EmployeeAction::TYPE_ACCOUNT_UNFROZEN,
            ]),
            'description' => fake()->sentence(),
            'metadata' => [
                'ip_address' => fake()->ipv4(),
            ],
        ];
    }
}
