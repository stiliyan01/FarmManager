<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Номенклатури
            RegionSeeder::class,
            SpeciesSeeder::class,
            MedicationSeeder::class,
            VaccinationSchemeSeeder::class,

            // Демо данни за област Шумен
            UserSeeder::class,
            FarmSeeder::class,
            AnimalSeeder::class,
            VetEventSeeder::class,
            InspectionSeeder::class,
        ]);
    }
}
