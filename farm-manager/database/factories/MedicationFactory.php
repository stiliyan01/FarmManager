<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MedicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'active_substance' => fake()->word(),
            'withdrawal_milk_days' => 5,
            'withdrawal_meat_days' => 21,
        ];
    }
}
