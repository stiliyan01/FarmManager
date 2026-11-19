<?php

use App\Enums\AnimalStatus;
use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Medication;
use App\Models\User;
use App\Models\VetEvent;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->vet = User::factory()->vet()->create();
    $this->farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $this->farm->id, 'vet_id' => $this->vet->id]);

    $this->antibiotic = Medication::factory()->create(['withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 21]);
});

// Данните от формата за нов запис. Тестът подава само това, което го интересува.
function vetEventData(array $data): array
{
    return array_merge([
        'type' => 'treatment',
        'medication_id' => null,
        'dose' => '5 ml',
        'event_date' => today()->toDateString(),
        'diagnosis' => 'Пневмония',
        'notes' => null,
        'animal_ids' => [],
    ], $data);
}

test('vet dashboard shows only the farms the vet is assigned to now', function () {
    $previousFarm = Farm::factory()->create();
    FarmVet::factory()->ended()->create(['farm_id' => $previousFarm->id, 'vet_id' => $this->vet->id]);
    Farm::factory()->create();

    $this->actingAs($this->vet)
        ->get(route('vet.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('vet/dashboard')
            ->has('farms', 1)
            ->where('farms.0.id', $this->farm->id)
        );
});

test('vet records a treatment and the quarantine is calculated on the server', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);
    $eventDate = today()->subDays(2);

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData([
            'medication_id' => $this->antibiotic->id,
            'event_date' => $eventDate->toDateString(),
            'animal_ids' => [$animal->id],
        ]))
        ->assertRedirect(route('animals.show', $animal));

    $event = VetEvent::first();

    expect($event->vet_id)->toBe($this->vet->id);
    expect($event->batch_id)->toBeNull();
    expect($event->milk_withdrawal_until->toDateString())->toBe($eventDate->copy()->addDays(5)->toDateString());
    expect($event->meat_withdrawal_until->toDateString())->toBe($eventDate->copy()->addDays(21)->toDateString());
    expect($animal->fresh()->isUnderQuarantine())->toBeTrue();
});

test('mass vaccination creates one record per animal with a shared batch id', function () {
    $vaccine = Medication::factory()->create(['withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0]);
    $animals = Animal::factory()->count(3)->create(['farm_id' => $this->farm->id]);

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData([
            'type' => 'vaccination',
            'medication_id' => $vaccine->id,
            'animal_ids' => $animals->pluck('id')->all(),
        ]))
        ->assertRedirect(route('farms.vet-events.index', $this->farm));

    $events = VetEvent::all();

    expect($events)->toHaveCount(3);
    expect($events->pluck('batch_id')->unique())->toHaveCount(1);
    expect($events->first()->batch_id)->not->toBeNull();
    expect($events->first()->milk_withdrawal_until)->toBeNull();
});

test('sold or dead animals cannot get new records', function () {
    $soldAnimal = Animal::factory()->create(['farm_id' => $this->farm->id, 'status' => AnimalStatus::Sold]);

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData(['type' => 'exam', 'animal_ids' => [$soldAnimal->id]]))
        ->assertSessionHasErrors('animal_ids.0');

    expect(VetEvent::count())->toBe(0);
});

test('animals from another farm cannot be added to the record', function () {
    $animalOnOtherFarm = Animal::factory()->create();

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData(['type' => 'exam', 'animal_ids' => [$animalOnOtherFarm->id]]))
        ->assertSessionHasErrors('animal_ids.0');
});

test('vaccination and treatment need a medication', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData(['type' => 'vaccination', 'animal_ids' => [$animal->id]]))
        ->assertSessionHasErrors('medication_id');

    // При преглед препарат не е нужен.
    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData(['type' => 'exam', 'animal_ids' => [$animal->id]]))
        ->assertSessionHasNoErrors();
});

test('vet cannot record on a farm without an active assignment', function () {
    $otherFarm = Farm::factory()->create();
    $animal = Animal::factory()->create(['farm_id' => $otherFarm->id]);

    $this->actingAs($this->vet)
        ->get(route('farms.vet-events.create', $otherFarm))
        ->assertForbidden();

    $this->actingAs($this->vet)
        ->post(route('farms.vet-events.store', $otherFarm), vetEventData(['type' => 'exam', 'animal_ids' => [$animal->id]]))
        ->assertForbidden();
});

test('owner sees the records of own farm but cannot add records', function () {
    $owner = User::find($this->farm->owner_id);
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);
    VetEvent::factory()->count(12)->create(['animal_id' => $animal->id, 'vet_id' => $this->vet->id]);

    $this->actingAs($owner)
        ->get(route('farms.vet-events.index', $this->farm))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('vet-events/index')
            ->has('events.data', 10)
            ->where('events.total', 12)
            ->where('canCreate', false)
        );

    $this->actingAs($owner)
        ->post(route('farms.vet-events.store', $this->farm), vetEventData(['type' => 'exam', 'animal_ids' => [$animal->id]]))
        ->assertForbidden();
});

test('editing a record recalculates the quarantine and goes to the activity log', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);
    $eventDate = today()->subDays(2);

    $event = VetEvent::factory()->create([
        'animal_id' => $animal->id,
        'vet_id' => $this->vet->id,
        'type' => VetEventType::Treatment,
        'medication_id' => $this->antibiotic->id,
        'dose' => '5 ml',
        'event_date' => $eventDate,
    ]);

    $this->actingAs($this->vet)
        ->put(route('vet-events.update', $event), vetEventData([
            'medication_id' => $this->antibiotic->id,
            'dose' => '8 ml',
            'event_date' => $eventDate->toDateString(),
        ]))
        ->assertRedirect(route('animals.show', $animal));

    $event->refresh();
    expect($event->dose)->toBe('8 ml');
    expect($event->meat_withdrawal_until->toDateString())->toBe($eventDate->copy()->addDays(21)->toDateString());

    // Правило 5: в журнала има кой е редактирал и старата и новата стойност.
    $log = Activity::where('subject_type', VetEvent::class)
        ->where('subject_id', $event->id)
        ->where('description', 'updated')
        ->first();

    expect($log->causer_id)->toBe($this->vet->id);
    expect($log->properties['old']['dose'])->toBe('5 ml');
    expect($log->properties['attributes']['dose'])->toBe('8 ml');
});

test('vet can edit and delete only own records', function () {
    $otherVet = User::factory()->vet()->create();
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);
    $otherVetRecord = VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $otherVet->id]);
    $ownRecord = VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $this->vet->id]);

    $this->actingAs($this->vet)->get(route('vet-events.edit', $otherVetRecord))->assertForbidden();
    $this->actingAs($this->vet)->delete(route('vet-events.destroy', $otherVetRecord))->assertForbidden();

    $this->actingAs($this->vet)
        ->delete(route('vet-events.destroy', $ownRecord))
        ->assertRedirect(route('animals.show', $animal));

    expect(VetEvent::find($ownRecord->id))->toBeNull();
    expect(VetEvent::find($otherVetRecord->id))->not->toBeNull();
});
