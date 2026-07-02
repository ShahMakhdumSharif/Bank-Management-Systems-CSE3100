<?php

namespace Database\Factories;

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
            'action_type' => fake()->randomElement([
                EmployeeAction::TYPE_CUSTOMER_APPROVED,
                EmployeeAction::TYPE_CUSTOMER_REJECTED,
                EmployeeAction::TYPE_ACCOUNT_FROZEN,
                EmployeeAction::TYPE_ACCOUNT_UNFROZEN,
            ]),
            'subject_type' => User::class,
            'subject_id' => User::factory()->state([
                'role' => User::ROLE_CUSTOMER,
            ]),
            'description' => fake()->sentence(),
            'metadata' => ['seeded' => false],
            'ip_address' => fake()->ipv4(),
        ];
    }
}
