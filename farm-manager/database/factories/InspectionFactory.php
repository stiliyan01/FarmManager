<?php

namespace Database\Factories;

use App\Enums\InspectionResult;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'inspector_id' => User::factory(),
            'inspected_at' => now()->subDays(10),
            'result' => InspectionResult::Ok,
            'findings' => null,
        ];
    }
}
