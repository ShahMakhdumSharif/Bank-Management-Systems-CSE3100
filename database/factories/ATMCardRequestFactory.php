<?php

namespace Database\Factories;

use App\Models\ATMCardRequest;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ATMCardRequest>
 */
class ATMCardRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'status' => ATMCardRequest::STATUS_PENDING,
            'handled_by' => null,
            'requested_at' => now(),
            'processed_at' => null,
            'rejection_reason' => null,
        ];
    }
}
