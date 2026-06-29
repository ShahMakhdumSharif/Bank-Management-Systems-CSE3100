<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->city();

        return [
            'name' => $city.' Branch',
            'code' => strtoupper(Str::random(3)).fake()->unique()->numberBetween(100, 999),
            'city' => $city,
            'address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
