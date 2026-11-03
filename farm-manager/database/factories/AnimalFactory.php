<?php

namespace Database\Factories;

use App\Enums\AnimalStatus;
use App\Models\Farm;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'ear_tag' => 'BG'.fake()->unique()->numerify('##########'),
            'species_id' => Species::factory(),
            'breed_id' => null,
            'sex' => fake()->randomElement(['female', 'male']),
            'birth_date' => fake()->dateTimeBetween('-6 years', '-2 months'),
            'status' => AnimalStatus::Active,
        ];
    }
}
