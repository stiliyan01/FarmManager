<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $breedsBySpecies = [
            'Овца' => ['Цигая', 'Плевенска черноглава', 'Асаф', 'Лакон', 'Тетевенска'],
            'Коза' => ['Българска бяла млечна', 'Зааненска', 'Алпийска'],
            'Говедо' => ['Българско черношарено', 'Холщайн-фризийска', 'Симентал', 'Българско сиво'],
        ];

        foreach ($breedsBySpecies as $speciesName => $breedNames) {
            $species = Species::create(['name' => $speciesName]);

            foreach ($breedNames as $breedName) {
                $species->breeds()->create(['name' => $breedName]);
            }
        }
    }
}
