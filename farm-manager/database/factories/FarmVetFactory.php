<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmVetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'vet_id' => User::factory()->vet(),
            'assigned_by' => User::factory(),
            'valid_from' => now()->subYear(),
            'valid_to' => null,
        ];
    }

    // Старо назначение, което вече е приключило.
    public function ended(): static
    {
        return $this->state(['valid_to' => now()->subMonth()]);
    }
}
