<?php

namespace App\Http\Controllers;

use App\Enums\AnimalStatus;
use App\Enums\InspectionResult;
use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Inspection;
use App\Models\Medication;
use App\Models\User;
use App\Models\VetEvent;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

// Журналът на действията (spatie/laravel-activitylog) – кой какво е създал, променил или изтрил.
class ActivityLogController extends Controller
{
    private const ACTIONS = [
        'created' => 'създаде',
        'updated' => 'редактира',
        'deleted' => 'изтри',
    ];

    private const SUBJECTS = [
        VetEvent::class => 'ветеринарен запис',
        Animal::class => 'животно',
        Farm::class => 'ферма',
        FarmVet::class => 'назначение на ветеринар',
        Inspection::class => 'проверка',
    ];

    private const FIELDS = [
        'type' => 'вид',
        'medication_id' => 'препарат',
        'dose' => 'доза',
        'event_date' => 'дата',
        'diagnosis' => 'диагноза',
        'notes' => 'бележки',
        'milk_withdrawal_until' => 'мляко до',
        'meat_withdrawal_until' => 'месо до',
        'status' => 'статус',
        'status_changed_at' => 'дата на статуса',
        'group_name' => 'група',
        'name' => 'име',
        'reg_number' => 'рег. №',
        'settlement' => 'населено място',
        'capacity' => 'капацитет',
        'owner_id' => 'собственик',
        'vet_id' => 'ветеринар',
        'valid_from' => 'от',
        'valid_to' => 'до',
        'result' => 'резултат',
        'findings' => 'констатации',
    ];

    private const DATE_FIELDS = [
        'event_date',
        'milk_withdrawal_until',
        'meat_withdrawal_until',
        'status_changed_at',
        'birth_date',
        'valid_from',
        'valid_to',
        'inspected_at',
    ];

    public function index()
    {
        $activities = Activity::with('causer', 'subject')
            ->orderByDesc('id')
            ->paginate(10)
            ->through(function (Activity $activity) {
                $action = self::ACTIONS[$activity->description] ?? $activity->description;
                $subject = self::SUBJECTS[$activity->subject_type] ?? 'запис';

                return [
                    'id' => $activity->id,
                    'date' => $activity->created_at->format('d.m.Y H:i'),
                    'user' => $activity->causer?->name ?? 'Системата',
                    'action' => $action.' '.$subject.' №'.$activity->subject_id,
                    'subject' => $this->subjectText($activity),
                    'changes' => $this->changes($activity),
                ];
            });

        return Inertia::render('activity-log/index', [
            'activities' => $activities,
        ]);
    }

    // Кое е животното или фермата – за да се разбира записът без да се търси по номер.
    private function subjectText(Activity $activity): ?string
    {
        $subject = $activity->subject;

        if ($subject instanceof VetEvent) {
            return $subject->animal->ear_tag.' · '.$subject->farm->name;
        }

        if ($subject instanceof Animal) {
            return $subject->ear_tag.' · '.$subject->farm->name;
        }

        if ($subject instanceof FarmVet) {
            return $subject->farm->name.' · '.$subject->vet->name;
        }

        if ($subject instanceof Inspection) {
            return $subject->farm->name;
        }

        if ($subject instanceof Farm) {
            return $subject->name;
        }

        // Записът вече е изтрит.
        return null;
    }

    // Старата и новата стойност на всяко променено поле. Показват се само при редакция.
    private function changes(Activity $activity): array
    {
        if ($activity->description !== 'updated') {
            return [];
        }

        $newValues = $activity->properties['attributes'] ?? [];
        $oldValues = $activity->properties['old'] ?? [];

        $changes = [];
        foreach ($newValues as $field => $newValue) {
            $changes[] = [
                'field' => self::FIELDS[$field] ?? $field,
                'old' => $this->formatValue($field, $oldValues[$field] ?? null),
                'new' => $this->formatValue($field, $newValue),
            ];
        }

        return $changes;
    }

    private function formatValue(string $field, $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        // Датите се пазят в журнала в UTC, напр. 2026-11-09T22:00:00.000000Z.
        if (in_array($field, self::DATE_FIELDS)) {
            return Carbon::parse($value)->setTimezone(config('app.timezone'))->format('d.m.Y');
        }

        return match ($field) {
            'type' => VetEventType::from($value)->label(),
            'status' => AnimalStatus::from($value)->label(),
            'result' => InspectionResult::from($value)->label(),
            'medication_id' => Medication::find($value)?->name ?? '№'.$value,
            'owner_id', 'vet_id' => User::find($value)?->name ?? '№'.$value,
            default => (string) $value,
        };
    }
}
