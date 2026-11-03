<?php

use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Breed;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Species;
use App\Models\User;
use App\Models\VetEvent;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->farm = Farm::factory()->create(['owner_id' => $this->owner->id]);
    $this->sheep = Species::factory()->create(['name' => 'Овца']);
});

test('owner sees the animals of own farm', function () {
    Animal::factory()->count(3)->create(['farm_id' => $this->farm->id, 'species_id' => $this->sheep->id]);

    $this->actingAs($this->owner)
        ->get(route('farms.animals.index', $this->farm))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('animals/index')->has('animals', 3));
});

test('owner can add an animal', function () {
    $breed = Breed::create(['species_id' => $this->sheep->id, 'name' => 'Цигая']);

    $response = $this->actingAs($this->owner)->post(route('farms.animals.store', $this->farm), [
        'ear_tag' => 'BG1234567890',
        'species_id' => $this->sheep->id,
        'breed_id' => $breed->id,
        'sex' => 'female',
        'birth_date' => '2024-03-15',
    ]);

    $animal = Animal::where('ear_tag', 'BG1234567890')->first();

    $response->assertRedirect(route('animals.show', $animal));
    expect($animal->farm_id)->toBe($this->farm->id);
    expect($animal->status)->toBe(AnimalStatus::Active);
});

test('ear tag must be unique and the breed must belong to the species', function () {
    $goat = Species::factory()->create(['name' => 'Коза']);
    $goatBreed = Breed::create(['species_id' => $goat->id, 'name' => 'Зааненска']);
    Animal::factory()->create(['ear_tag' => 'BG1111111111']);

    $this->actingAs($this->owner)
        ->post(route('farms.animals.store', $this->farm), [
            'ear_tag' => 'BG1111111111',
            'species_id' => $this->sheep->id,
            'breed_id' => $goatBreed->id,
            'sex' => 'female',
        ])
        ->assertSessionHasErrors(['ear_tag', 'breed_id']);
});

test('vet cannot add animals', function () {
    $vet = User::factory()->vet()->create();
    FarmVet::factory()->create(['farm_id' => $this->farm->id, 'vet_id' => $vet->id]);

    $this->actingAs($vet)
        ->post(route('farms.animals.store', $this->farm), [
            'ear_tag' => 'BG2222222222',
            'species_id' => $this->sheep->id,
            'sex' => 'female',
        ])
        ->assertForbidden();
});

test('animal profile shows the quarantine', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id, 'species_id' => $this->sheep->id]);
    VetEvent::factory()->create([
        'animal_id' => $animal->id,
        'milk_withdrawal_until' => today()->addDays(4),
    ]);

    $this->actingAs($this->owner)
        ->get(route('animals.show', $animal))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('animals/show')
            ->where('quarantine.milk.days_left', 4)
            ->where('quarantine.meat', null)
        );
});

test('another owner cannot open the animal profile', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);
    $otherOwner = User::factory()->create();

    $this->actingAs($otherOwner)
        ->get(route('animals.show', $animal))
        ->assertForbidden();
});

test('owner can mark an animal as sold, but only once', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);

    $this->actingAs($this->owner)
        ->put(route('animals.status.update', $animal), ['status' => 'sold', 'status_date' => today()->toDateString()])
        ->assertSessionHasNoErrors();

    expect($animal->fresh()->status)->toBe(AnimalStatus::Sold);

    $this->actingAs($this->owner)
        ->put(route('animals.status.update', $animal), ['status' => 'dead', 'status_date' => today()->toDateString()])
        ->assertSessionHasErrors('status');

    expect($animal->fresh()->status)->toBe(AnimalStatus::Sold);
});

test('owner can record a weight', function () {
    $animal = Animal::factory()->create(['farm_id' => $this->farm->id]);

    $this->actingAs($this->owner)
        ->post(route('animals.weights.store', $animal), ['weight_kg' => 62.5, 'measured_at' => today()->toDateString()])
        ->assertSessionHasNoErrors();

    expect($animal->weights()->first()->weight_kg)->toBe('62.5');
});
