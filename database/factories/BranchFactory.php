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
            'branch_code' => strtoupper(Str::random(3)).fake()->unique()->numberBetween(100, 999),
            'address' => fake()->streetAddress(),
            'city' => $city,
            'country_code' => 'BD',
            'is_active' => true,
        ];
    }
}
