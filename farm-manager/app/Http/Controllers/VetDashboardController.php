<?php

namespace App\Http\Controllers;

use App\Actions\CheckFarmAnimals;
use Illuminate\Http\Request;
use Inertia\Inertia;

// „Моят ден“ – таблото на ветеринаря: назначените ферми и какво предстои в тях.
class VetDashboardController extends Controller
{
    public function index(Request $request, CheckFarmAnimals $checkFarmAnimals)
    {
        $farms = $request->user()->assignedFarms()->orderBy('name')->get();

        $farmRows = [];
        $upcoming = [];
        $totals = ['animals' => 0, 'quarantined' => 0, 'overdue' => 0, 'upcoming' => 0];

        foreach ($farms as $farm) {
            $check = $checkFarmAnimals->handle($farm);

            $farmRow = [
                'id' => $farm->id,
                'name' => $farm->name,
                'settlement' => $farm->settlement,
                'animals' => $check['animals']->count(),
                'quarantined' => count($check['quarantined']),
                'overdue' => count($check['overdue']),
                'upcoming' => count($check['upcoming']),
            ];
            $farmRows[] = $farmRow;

            $totals['animals'] += $farmRow['animals'];
            $totals['quarantined'] += $farmRow['quarantined'];
            $totals['overdue'] += $farmRow['overdue'];
            $totals['upcoming'] += $farmRow['upcoming'];

            // Една и съща ваксина в една ферма на една дата е един ред с броя животни.
            foreach ($check['upcoming'] as $vaccination) {
                $key = $farm->id.'|'.$vaccination['scheme'].'|'.$vaccination['due_date']->toDateString();

                if (! isset($upcoming[$key])) {
                    $upcoming[$key] = [
                        'key' => $key,
                        'farm_id' => $farm->id,
                        'farm' => $farm->name,
                        'scheme' => $vaccination['scheme'],
                        'due_date' => $vaccination['due_date'],
                        'animal_count' => 0,
                    ];
                }

                $upcoming[$key]['animal_count']++;
            }
        }

        usort($upcoming, fn ($a, $b) => $a['due_date'] <=> $b['due_date']);

        foreach ($upcoming as $index => $row) {
            $upcoming[$index]['due_date'] = $row['due_date']->isoFormat('dd, D.MM');
        }

        return Inertia::render('vet/dashboard', [
            'today' => today()->isoFormat('dddd, D MMMM YYYY'),
            'farms' => $farmRows,
            'totals' => $totals,
            'upcoming' => $upcoming,
        ]);
    }
}
