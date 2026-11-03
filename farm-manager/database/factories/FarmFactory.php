<?php

namespace Database\Factories;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmFactory extends Factory
{
    public function definition(): array
    {
        return [
            'region_id' => Region::factory(),
            'reg_number' => fake()->unique()->numerify('2700-####'),
            'name' => 'Ферма '.fake()->lastName(),
            'settlement' => fake()->city(),
            'owner_id' => User::factory(),
            'capacity' => fake()->numberBetween(50, 300),
            'status' => 'active',
        ];
    }
}
