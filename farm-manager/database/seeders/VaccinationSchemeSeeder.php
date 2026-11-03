<?php

namespace Database\Seeders;

use App\Models\Medication;
use App\Models\Species;
use App\Models\VaccinationScheme;
use Illuminate\Database\Seeder;

class VaccinationSchemeSeeder extends Seeder
{
    public function run(): void
    {
        $sheep = Species::where('name', 'Овца')->first();
        $goat = Species::where('name', 'Коза')->first();
        $cattle = Species::where('name', 'Говедо')->first();

        $anthrax = Medication::where('name', 'Ваксина срещу антракс')->first();
        $blueTongue = Medication::where('name', 'Ваксина срещу син език')->first();
        $enterotoxemia = Medication::where('name', 'Ваксина срещу ентеротоксемия')->first();

        VaccinationScheme::create(['species_id' => $sheep->id, 'name' => 'Антракс', 'medication_id' => $anthrax->id, 'interval_days' => 365, 'mandatory' => true]);
        VaccinationScheme::create(['species_id' => $sheep->id, 'name' => 'Син език', 'medication_id' => $blueTongue->id, 'interval_days' => 365, 'mandatory' => true]);
        VaccinationScheme::create(['species_id' => $sheep->id, 'name' => 'Ентеротоксемия', 'medication_id' => $enterotoxemia->id, 'interval_days' => 180, 'mandatory' => false]);

        VaccinationScheme::create(['species_id' => $goat->id, 'name' => 'Антракс', 'medication_id' => $anthrax->id, 'interval_days' => 365, 'mandatory' => true]);
        VaccinationScheme::create(['species_id' => $goat->id, 'name' => 'Ентеротоксемия', 'medication_id' => $enterotoxemia->id, 'interval_days' => 180, 'mandatory' => false]);

        VaccinationScheme::create(['species_id' => $cattle->id, 'name' => 'Антракс', 'medication_id' => $anthrax->id, 'interval_days' => 365, 'mandatory' => true]);
        VaccinationScheme::create(['species_id' => $cattle->id, 'name' => 'Син език', 'medication_id' => $blueTongue->id, 'interval_days' => 365, 'mandatory' => true]);
    }
}
