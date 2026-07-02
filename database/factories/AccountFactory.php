<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_APPROVED,
            ]),
            'branch_id' => Branch::factory(),
            'account_number' => fake()->unique()->numerify('10##########'),
            'account_type' => Account::TYPE_SAVINGS,
            'balance' => fake()->randomFloat(2, 500, 50000),
            'status' => Account::STATUS_ACTIVE,
            'approved_by' => null,
            'frozen_by' => null,
            'approved_at' => now(),
            'freeze_reason' => null,
            'frozen_at' => null,
        ];
    }

    public function frozen(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Account::STATUS_FROZEN,
            'freeze_reason' => 'Suspicious account activity review',
            'frozen_at' => now(),
        ]);
    }
}
