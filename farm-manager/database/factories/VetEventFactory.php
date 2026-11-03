<?php

namespace Database\Factories;

use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VetEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            // Фермата се взима от животното, за да съвпадат.
            'farm_id' => function (array $attributes) {
                return Animal::find($attributes['animal_id'])->farm_id;
            },
            'vet_id' => User::factory()->vet(),
            'type' => VetEventType::Exam,
            'event_date' => now()->subDays(3),
            'diagnosis' => null,
            'notes' => null,
        ];
    }
}
