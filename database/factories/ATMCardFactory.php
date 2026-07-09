<?php

namespace Database\Factories;

use App\Models\ATMCard;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<ATMCard>
 */
class ATMCardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'atm_card_request_id' => null,
            'card_number' => '5060'.fake()->unique()->numerify('############'),
            'pin_hash' => Hash::make('1234'),
            'status' => ATMCard::STATUS_ACTIVE,
            'failed_attempts' => 0,
            'issued_by' => User::factory()->employee(),
            'issued_at' => now(),
            'expires_at' => now()->addYears(5),
            'last_used_at' => null,
        ];
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ATMCard::STATUS_BLOCKED,
            'failed_attempts' => 0,
        ]);
    }
}
