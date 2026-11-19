<?php

namespace App\Http\Controllers;

use App\Actions\SummarizeRegion;
use App\Models\Inspection;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Контролното табло на инспектора – как се изпълнява програмата в областта.
class ControlController extends Controller
{
    public function index(Request $request, SummarizeRegion $summarizeRegion)
    {
        $region = $request->user()->region;
        $summaries = $summarizeRegion->handle($region);

        $totals = [
            'animals' => 0,
            'quarantined' => 0,
            'overdue' => 0,
            'farms_with_overdue' => 0,
            'vaccinations_total' => 0,
            'vaccinations_on_time' => 0,
        ];

        foreach ($summaries as $summary) {
            $overdueCount = count($summary['overdue_animals']);

            $totals['animals'] += $summary['animals'];
            $totals['quarantined'] += $summary['quarantined'];
            $totals['overdue'] += $overdueCount;
            $totals['vaccinations_total'] += $summary['vaccinations_total'];
            $totals['vaccinations_on_time'] += $summary['vaccinations_on_time'];

            if ($overdueCount > 0) {
                $totals['farms_with_overdue']++;
            }
        }

        $vetRows = $this->vetRows($summaries);

        return Inertia::render('control/index', [
            'region' => $region->name,
            'year' => today()->year,
            'stats' => [
                'farms' => count($summaries),
                'vets' => count($vetRows),
                'animals' => $totals['animals'],
                'program_percent' => $this->percent($totals['vaccinations_on_time'], $totals['vaccinations_total']),
                'overdue' => $totals['overdue'],
                'farms_with_overdue' => $totals['farms_with_overdue'],
                'quarantined' => $totals['quarantined'],
            ],
            'vets' => $vetRows,
            'signals' => $this->signals($summaries),
            'recentInspections' => $this->recentInspections($region->id),
        ]);
    }

    // „Изпълнение по ветеринар“ – фермите на всеки ветеринар събрани на един ред. Най-слабите са първи.
    private function vetRows(array $summaries): array
    {
        $rows = [];

        foreach ($summaries as $summary) {
            $vet = $summary['vet'];
            if ($vet === null) {
                continue;
            }

            if (! isset($rows[$vet->id])) {
                $rows[$vet->id] = [
                    'id' => $vet->id,
                    'name' => $vet->name,
                    'farms' => 0,
                    'animals' => 0,
                    'overdue' => 0,
                    'late' => 0,
                    'vaccinations_total' => 0,
                    'vaccinations_on_time' => 0,
                ];
            }

            $rows[$vet->id]['farms']++;
            $rows[$vet->id]['animals'] += $summary['animals'];
            $rows[$vet->id]['overdue'] += count($summary['overdue_animals']);
            $rows[$vet->id]['vaccinations_total'] += $summary['vaccinations_total'];
            $rows[$vet->id]['vaccinations_on_time'] += $summary['vaccinations_on_time'];

            // Броят се само закъснелите записи, въведени от самия ветеринар.
            foreach ($summary['late_events'] as $event) {
                if ($event->vet_id === $vet->id) {
                    $rows[$vet->id]['late']++;
                }
            }
        }

        foreach ($rows as $vetId => $row) {
            $percent = $this->percent($row['vaccinations_on_time'], $row['vaccinations_total']);
            $rating = $this->rating($row['overdue'], $row['late'], $percent);

            $rows[$vetId]['percent'] = $percent;
            $rows[$vetId]['rating'] = $rating;
            $rows[$vetId]['rating_label'] = $this->ratingLabel($rating);
        }

        $rows = array_values($rows);
        usort($rows, fn ($a, $b) => $a['percent'] <=> $b['percent']);

        return $rows;
    }

    // Процент на задължителните ваксинации, които са в срок. null – няма какво да се ваксинира.
    private function percent(int $onTime, int $total): ?int
    {
        if ($total === 0) {
            return null;
        }

        return (int) round($onTime / $total * 100);
    }

    // „в норма“ – няма просрочени и закъснели; „риск“ – под 80% в срок; иначе „наблюдение“.
    private function rating(int $overdue, int $late, ?int $percent): string
    {
        if ($overdue === 0 && $late === 0) {
            return 'ok';
        }

        if ($percent !== null && $percent < 80) {
            return 'risk';
        }

        return 'watch';
    }

    private function ratingLabel(string $rating): string
    {
        return match ($rating) {
            'ok' => 'в норма',
            'watch' => 'наблюдение',
            'risk' => 'риск',
        };
    }

    // Червено – просрочени ваксинации. Кехлибарено – закъснели записи и ферми без ветеринар.
    private function signals(array $summaries): array
    {
        $redSignals = [];
        $amberSignals = [];

        foreach ($summaries as $summary) {
            $farm = $summary['farm'];
            $vetName = $summary['vet']?->name ?? 'без ветеринар';

            if (count($summary['overdue_animals']) > 0) {
                $redSignals[] = [
                    'key' => 'overdue-'.$farm->id,
                    'color' => 'red',
                    'title' => 'Просрочена ваксинация – '.$this->animalsText(count($summary['overdue_animals'])),
                    'details' => $farm->name.' · '.$vetName,
                    'farm_id' => $farm->id,
                    'animals' => $summary['overdue_animals'],
                ];
            }

            if (count($summary['late_events']) > 0) {
                // Едно животно може да има няколко закъснели записа – показваме го веднъж.
                $animals = [];
                foreach ($summary['late_events'] as $event) {
                    $animals[$event->animal_id] = ['id' => $event->animal_id, 'ear_tag' => $event->animal->ear_tag];
                }

                $amberSignals[] = [
                    'key' => 'late-'.$farm->id,
                    'color' => 'amber',
                    'title' => 'Записи, въведени след повече от 7 дни – '.count($summary['late_events']),
                    'details' => $farm->name.' · '.$vetName,
                    'farm_id' => $farm->id,
                    'animals' => array_values($animals),
                ];
            }

            if ($summary['vet'] === null) {
                $amberSignals[] = [
                    'key' => 'no-vet-'.$farm->id,
                    'color' => 'amber',
                    'title' => 'Ферма без обслужващ ветеринар',
                    'details' => $farm->name.' · '.$farm->settlement,
                    'farm_id' => $farm->id,
                    'animals' => [],
                ];
            }
        }

        return array_merge($redSignals, $amberSignals);
    }

    private function animalsText(int $count): string
    {
        if ($count === 1) {
            return '1 животно';
        }

        return $count.' животни';
    }

    private function recentInspections(int $regionId): array
    {
        $inspections = Inspection::with('farm')
            ->whereHas('farm', function ($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })
            ->orderByDesc('inspected_at')
            ->limit(5)
            ->get();

        $rows = [];
        foreach ($inspections as $inspection) {
            $rows[] = [
                'id' => $inspection->id,
                'farm_id' => $inspection->farm_id,
                'farm' => $inspection->farm->name,
                'date' => $inspection->inspected_at->format('d.m.Y'),
                'result' => $inspection->result->value,
                'result_label' => $inspection->result->label(),
            ];
        }

        return $rows;
    }
}
