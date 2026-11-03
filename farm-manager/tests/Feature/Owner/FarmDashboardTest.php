<?php

use App\Enums\VetEventType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Medication;
use App\Models\Species;
use App\Models\User;
use App\Models\VaccinationScheme;
use App\Models\VetEvent;
use Inertia\Testing\AssertableInertia as Assert;

test('owner is sent from the dashboard to the farm page', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);

    $this->actingAs($owner)
        ->get('/dashboard')
        ->assertRedirect(route('farms.show', $farm));
});

test('owner without a farm sees the placeholder dashboard', function () {
    $owner = User::factory()->create();

    $this->actingAs($owner)
        ->get('/dashboard')
        ->assertInertia(fn (Assert $page) => $page->component('dashboard'));
});

test('owner cannot open another owner\'s farm', function () {
    $owner = User::factory()->create();
    $otherFarm = Farm::factory()->create();

    $this->actingAs($owner)
        ->get(route('farms.show', $otherFarm))
        ->assertForbidden();
});

test('farm dashboard counts animals, quarantine and overdue vaccinations', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $sheep = Species::factory()->create(['name' => 'Овца']);
    $vaccine = Medication::factory()->create(['withdrawal_milk_days' => 0, 'withdrawal_meat_days' => 0]);
    $antibiotic = Medication::factory()->create(['withdrawal_milk_days' => 5, 'withdrawal_meat_days' => 20]);

    VaccinationScheme::create([
        'species_id' => $sheep->id,
        'name' => 'Антракс',
        'medication_id' => $vaccine->id,
        'interval_days' => 365,
        'mandatory' => true,
    ]);

    // Ваксинирано преди месец и лекувано преди 2 дни – под карантина, но ваксината е наред.
    $treatedAnimal = Animal::factory()->create(['farm_id' => $farm->id, 'species_id' => $sheep->id]);
    VetEvent::factory()->create([
        'animal_id' => $treatedAnimal->id,
        'type' => VetEventType::Vaccination,
        'medication_id' => $vaccine->id,
        'event_date' => today()->subMonth(),
    ]);
    VetEvent::factory()->create([
        'animal_id' => $treatedAnimal->id,
        'type' => VetEventType::Treatment,
        'medication_id' => $antibiotic->id,
        'event_date' => today()->subDays(2),
        'meat_withdrawal_until' => today()->addDays(18),
    ]);

    // Никога не е ваксинирано – просрочено.
    Animal::factory()->create(['farm_id' => $farm->id, 'species_id' => $sheep->id]);

    $this->actingAs($owner)
        ->get(route('farms.show', $farm))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('farms/show')
            ->where('stats.animals', 2)
            ->where('stats.quarantined', 1)
            ->where('stats.overdue', 1)
            ->where('canAddAnimal', true)
        );
});

test('assigned vet can open the farm page, but cannot add animals', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    $this->actingAs($vet)
        ->get(route('farms.show', $farm))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('canAddAnimal', false));
});
