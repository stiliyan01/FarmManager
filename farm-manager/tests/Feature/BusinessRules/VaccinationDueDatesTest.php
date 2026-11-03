<?php

use App\Actions\CalculateVaccinationDueDates;
use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\VaccinationScheme;
use App\Models\VetEvent;

// Помощна функция: животно с дадени ветеринарни записи (без база данни).
function animalWithEvents(array $events): Animal
{
    $animal = new Animal(['species_id' => 1]);
    $animal->setRelation('vetEvents', collect($events));

    return $animal;
}

function anthraxScheme(): VaccinationScheme
{
    return new VaccinationScheme(['species_id' => 1, 'name' => 'Антракс', 'medication_id' => 7, 'interval_days' => 365]);
}

test('next vaccination is the last vaccination plus the scheme interval', function () {
    $animal = animalWithEvents([
        new VetEvent(['type' => VetEventType::Vaccination, 'medication_id' => 7, 'event_date' => today()->subDays(100)]),
    ]);

    $result = (new CalculateVaccinationDueDates)->handle($animal, collect([anthraxScheme()]));

    expect($result[0]['due_date']->toDateString())->toBe(today()->addDays(265)->toDateString());
    expect($result[0]['is_overdue'])->toBeFalse();
});

test('vaccination is overdue when the due date has passed', function () {
    $animal = animalWithEvents([
        new VetEvent(['type' => VetEventType::Vaccination, 'medication_id' => 7, 'event_date' => today()->subDays(400)]),
    ]);

    $result = (new CalculateVaccinationDueDates)->handle($animal, collect([anthraxScheme()]));

    expect($result[0]['is_overdue'])->toBeTrue();
});

test('an animal that was never vaccinated is overdue', function () {
    $animal = animalWithEvents([]);

    $result = (new CalculateVaccinationDueDates)->handle($animal, collect([anthraxScheme()]));

    expect($result[0]['last_date'])->toBeNull();
    expect($result[0]['is_overdue'])->toBeTrue();
});

test('schemes for other species are skipped', function () {
    $animal = animalWithEvents([]);
    $cattleScheme = new VaccinationScheme(['species_id' => 2, 'name' => 'Син език', 'medication_id' => 8, 'interval_days' => 365]);

    $result = (new CalculateVaccinationDueDates)->handle($animal, collect([$cattleScheme]));

    expect($result)->toBe([]);
});
