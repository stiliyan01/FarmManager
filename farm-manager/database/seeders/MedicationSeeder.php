<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        // Карантинните срокове са примерни – само за демонстрация.
        $medications = [
            ['name' => 'Пен-Стреп', 'active_substance' => 'бензилпеницилин + дихидрострептомицин', 'withdrawal_milk_days' => 3, 'withdrawal_meat_days' => 23],
            ['name' => 'Окситетрациклин 20% LA', 'active_substance' => 'окситетрациклин', 'withdrawal_milk_days' => 7, 'withdrawal_meat_days' => 28],
            ['name' => 'Ивермектин 1%', 'active_substance' => 'ивермектин', 'withdrawal_milk_days' => 28, 'withdrawal_meat_days' => 42],
            ['name' => 'Албендазол 10%', 'active_substance' => 'албендазол', 'withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 14],
            ['name' => 'Мелоксикам 20 mg/ml', 'active_substance' => 'мелоксикам', 'withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 15],
            ['name' => 'Енрофлоксацин 10%', 'active_substance' => 'енрофлоксацин', 'withdrawal_milk_days' => 4, 'withdrawal_meat_days' => 14],

            // Ваксини
            ['name' => 'Ваксина срещу антракс', 'active_substance' => 'спори на Bacillus anthracis', 'withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0],
            ['name' => 'Ваксина срещу син език', 'active_substance' => 'инактивиран вирус BTV-3/4/8', 'withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0],
            ['name' => 'Ваксина срещу ентеротоксемия', 'active_substance' => 'токсоиди на Clostridium spp.', 'withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
}
