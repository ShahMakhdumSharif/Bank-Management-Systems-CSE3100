<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\TransferRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferRequest>
 */
class TransferRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_account_id' => Account::factory(),
            'receiver_account_id' => Account::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'status' => TransferRequest::STATUS_PENDING,
            'handled_by' => null,
            'requested_at' => now(),
            'processed_at' => null,
            'rejection_reason' => null,
        ];
    }
}
