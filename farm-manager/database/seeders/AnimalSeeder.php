<?php

namespace Database\Seeders;

use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Species;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $sheep = Species::where('name', 'Овца')->first();
        $goat = Species::where('name', 'Коза')->first();
        $cattle = Species::where('name', 'Говедо')->first();

        // Общо 150 животни
        $this->createHerd(Farm::where('name', 'Ферма Мадара')->first(), $sheep, 45);
        $this->createHerd(Farm::where('name', 'Козеферма Каспичан')->first(), $goat, 30);
        $this->createHerd(Farm::where('name', 'Кравеферма Нови пазар')->first(), $cattle, 25);
        $this->createHerd(Farm::where('name', 'Ферма Хан Крум')->first(), $sheep, 35);
        $this->createHerd(Farm::where('name', 'Ферма Хан Крум')->first(), $goat, 15);

        // Едно продадено и едно умряло животно, за да има и такива статуси.
        $soldAnimal = Animal::where('group_name', 'Мъжки')->first();
        $soldAnimal->update([
            'status' => AnimalStatus::Sold,
            'status_changed_at' => now()->subWeeks(2),
        ]);

        $deadAnimal = Animal::where('group_name', 'Основно стадо')->latest('id')->first();
        $deadAnimal->update([
            'status' => AnimalStatus::Dead,
            'status_changed_at' => now()->subDays(5),
            'notes' => 'Умряло след раждане.',
        ]);
    }

    // Създава стадо от един вид: възрастни животни и малко приплод от тях.
    private function createHerd(Farm $farm, Species $species, int $count): void
    {
        $youngCount = intdiv($count, 5);
        $adultCount = $count - $youngCount;

        for ($i = 0; $i < $adultCount; $i++) {
            $isFemale = fake()->boolean(85);

            $animal = Animal::factory()->create([
                'farm_id' => $farm->id,
                'species_id' => $species->id,
                'breed_id' => $species->breeds->random()->id,
                'sex' => $isFemale ? 'female' : 'male',
                'birth_date' => fake()->dateTimeBetween('-7 years', '-1 year'),
                'group_name' => $isFemale ? 'Основно стадо' : 'Мъжки',
            ]);

            $this->addWeight($animal, $this->typicalWeight($species), today()->subMonths(6));
            $this->addWeight($animal, $this->typicalWeight($species), today()->subWeek());
        }

        $mothers = $farm->animals()
            ->where('species_id', $species->id)
            ->where('sex', 'female')
            ->get();

        for ($i = 0; $i < $youngCount; $i++) {
            $mother = $mothers->random();

            $animal = Animal::factory()->create([
                'farm_id' => $farm->id,
                'species_id' => $species->id,
                'breed_id' => $mother->breed_id,
                'mother_id' => $mother->id,
                'birth_date' => fake()->dateTimeBetween('-5 months', '-1 month'),
                'group_name' => 'Приплод',
            ]);

            $this->addWeight($animal, $this->typicalWeight($species) / 4, today()->subWeek());
        }
    }

    private function addWeight(Animal $animal, float $typicalWeight, $measuredAt): void
    {
        $animal->weights()->create([
            'weight_kg' => round($typicalWeight * fake()->randomFloat(2, 0.8, 1.2), 1),
            'measured_at' => $measuredAt,
        ]);
    }

    private function typicalWeight(Species $species): int
    {
        return match ($species->name) {
            'Говедо' => 550,
            'Коза' => 50,
            default => 60,
        };
    }
}
