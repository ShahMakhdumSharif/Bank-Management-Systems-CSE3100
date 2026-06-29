<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 5000);
        $balanceAfter = fake()->randomFloat(2, $amount, 75000);

        return [
            'account_id' => Account::factory(),
            'performed_by' => User::factory()->state([
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_APPROVED,
            ]),
            'transaction_number' => 'TXN'.fake()->unique()->numerify('##########'),
            'type' => fake()->randomElement([
                Transaction::TYPE_DEPOSIT,
                Transaction::TYPE_WITHDRAWAL,
                Transaction::TYPE_TRANSFER_IN,
                Transaction::TYPE_TRANSFER_OUT,
            ]),
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'reference' => fake()->optional()->bothify('REF-####??'),
            'description' => fake()->sentence(),
            'occurred_at' => now(),
        ];
    }
}
