<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SpeciesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Овца', 'Коза', 'Говедо']),
        ];
    }
}
