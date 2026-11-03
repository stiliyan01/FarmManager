<?php

namespace Database\Seeders;

use App\Enums\InspectionResult;
use App\Models\Farm;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Database\Seeder;

class InspectionSeeder extends Seeder
{
    public function run(): void
    {
        $inspector = User::where('email', 'inspector@stado.test')->first();

        Inspection::create([
            'farm_id' => Farm::where('name', 'Ферма Мадара')->first()->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => today()->subMonths(3),
            'result' => InspectionResult::Ok,
            'findings' => 'Документацията е в ред. Животните са идентифицирани.',
        ]);

        Inspection::create([
            'farm_id' => Farm::where('name', 'Кравеферма Нови пазар')->first()->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => today()->subMonths(2),
            'result' => InspectionResult::Ok,
            'findings' => null,
        ]);

        Inspection::create([
            'farm_id' => Farm::where('name', 'Ферма Хан Крум')->first()->id,
            'inspector_id' => $inspector->id,
            'inspected_at' => today()->subWeeks(3),
            'result' => InspectionResult::Violation,
            'findings' => 'Пропусната задължителна ваксинация срещу антракс.',
        ]);
    }
}
