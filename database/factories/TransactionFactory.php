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
            'related_account_id' => null,
            'transfer_request_id' => null,
            'reference' => 'TXN'.fake()->unique()->numerify('##########'),
            'type' => fake()->randomElement([
                Transaction::TYPE_ATM_DEPOSIT,
                Transaction::TYPE_ATM_WITHDRAWAL,
                Transaction::TYPE_ADJUSTMENT,
            ]),
            'amount' => $amount,
            'balance_before' => $balanceAfter - $amount,
            'balance_after' => $balanceAfter,
            'status' => Transaction::STATUS_COMPLETED,
            'source' => Transaction::SOURCE_EMPLOYEE,
            'description' => fake()->sentence(),
            'handled_by' => User::factory()->state([
                'role' => User::ROLE_EMPLOYEE,
                'status' => User::STATUS_APPROVED,
            ]),
        ];
    }
}
