<?php

namespace App\Http\Controllers;

use App\Actions\SummarizeRegion;
use App\Exports\ReportExport;
use App\Models\Farm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

// Отчети за изтегляне – всеки може да се свали като Excel или като PDF.
class ReportController extends Controller
{
    // Страницата „Отчети“ на фермата.
    public function index(Farm $farm)
    {
        Gate::authorize('view', $farm);

        return Inertia::render('reports/index', [
            'farm' => $farm->only('id', 'name'),
            // Периодът по подразбиране е от началото на годината до днес.
            'defaultFrom' => today()->startOfYear()->toDateString(),
            'defaultTo' => today()->toDateString(),
        ]);
    }

    // Ветеринарните записи за период – дневникът на фермата.
    public function vetEvents(Request $request, Farm $farm, string $format)
    {
        Gate::authorize('view', $farm);

        $period = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = Carbon::parse($period['from']);
        $to = Carbon::parse($period['to']);

        $events = $farm->vetEvents()
            ->with('animal', 'medication', 'vet')
            ->whereDate('event_date', '>=', $from)
            ->whereDate('event_date', '<=', $to)
            ->orderBy('event_date')
            ->get();

        $rows = [];
        foreach ($events as $event) {
            $rows[] = [
                $event->event_date->format('d.m.Y'),
                $event->animal->ear_tag,
                $event->type->label(),
                $event->medication?->name,
                $event->dose,
                $event->diagnosis,
                $event->vet->name,
                $event->milk_withdrawal_until?->format('d.m.Y'),
                $event->meat_withdrawal_until?->format('d.m.Y'),
            ];
        }

        return $this->download($format, [
            'fileName' => 'veterinarni-zapisi-'.$farm->reg_number,
            'title' => 'Ветеринарни записи – '.$farm->name,
            'subtitle' => 'Рег. № '.$farm->reg_number.' · от '.$from->format('d.m.Y').' до '.$to->format('d.m.Y'),
            'headings' => ['Дата', 'Ушна марка', 'Вид', 'Препарат', 'Доза', 'Диагноза', 'Ветеринар', 'Мляко до', 'Месо до'],
            'rows' => $rows,
        ]);
    }

    // Регистърът на животните – всички, и продадените, и умрелите.
    public function animals(Farm $farm, string $format)
    {
        Gate::authorize('view', $farm);

        $animals = $farm->animals()
            ->with('species', 'breed', 'mother', 'vetEvents')
            ->orderBy('ear_tag')
            ->get();

        $rows = [];
        foreach ($animals as $animal) {
            $rows[] = [
                $animal->ear_tag,
                $animal->species->name,
                $animal->breed?->name,
                $animal->sexLabel(),
                $animal->birth_date?->format('d.m.Y'),
                $animal->mother?->ear_tag,
                $animal->group_name,
                $animal->status->label(),
                $animal->quarantineUntil()?->format('d.m.Y'),
            ];
        }

        return $this->download($format, [
            'fileName' => 'zhivotni-'.$farm->reg_number,
            'title' => 'Регистър на животните – '.$farm->name,
            'subtitle' => 'Рег. № '.$farm->reg_number.' · към '.today()->format('d.m.Y').' · животни: '.count($rows),
            'headings' => ['Ушна марка', 'Вид', 'Порода', 'Пол', 'Роден/а', 'Майка', 'Група', 'Статус', 'Карантина до'],
            'rows' => $rows,
        ]);
    }

    // Отчетът по област на инспектора – по един ред за всяка ферма.
    public function region(Request $request, string $format, SummarizeRegion $summarizeRegion)
    {
        $region = $request->user()->region;

        $rows = [];
        foreach ($summarizeRegion->handle($region) as $summary) {
            $farm = $summary['farm'];

            $onTime = '—';
            if ($summary['vaccinations_total'] > 0) {
                $onTime = round($summary['vaccinations_on_time'] / $summary['vaccinations_total'] * 100).'%';
            }

            $lastInspection = '—';
            if ($summary['last_inspection'] !== null) {
                $lastInspection = $summary['last_inspection']->inspected_at->format('d.m.Y').' · '.$summary['last_inspection']->result->label();
            }

            $rows[] = [
                $farm->reg_number,
                $farm->name,
                $farm->settlement,
                $farm->owner?->name ?? '—',
                $summary['vet']?->name ?? '—',
                $summary['animals'],
                $summary['quarantined'],
                count($summary['overdue_animals']),
                $onTime,
                count($summary['late_events']),
                $lastInspection,
            ];
        }

        return $this->download($format, [
            'fileName' => 'otchet-oblast-'.today()->format('Y-m-d'),
            'title' => 'Изпълнение на профилактичната програма – област '.$region->name,
            'subtitle' => 'Към '.today()->format('d.m.Y').' · ферми: '.count($rows),
            'headings' => ['Рег. №', 'Ферма', 'Населено място', 'Собственик', 'Ветеринар', 'Животни', 'Под карантина', 'Просрочена ваксинация', 'Ваксинации в срок', 'Закъснели записи', 'Последна проверка'],
            'rows' => $rows,
        ]);
    }

    private function download(string $format, array $report)
    {
        if ($format === 'excel') {
            return Excel::download(new ReportExport($report['headings'], $report['rows']), $report['fileName'].'.xlsx');
        }

        return Pdf::loadView('reports.table', $report)
            ->setPaper('a4', 'landscape')
            ->download($report['fileName'].'.pdf');
    }
}
