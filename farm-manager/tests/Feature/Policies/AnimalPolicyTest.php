<?php

use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\User;

test('owner can add animals only to own farm', function () {
    $owner = User::factory()->create();
    $ownFarm = Farm::factory()->create(['owner_id' => $owner->id]);
    $otherFarm = Farm::factory()->create();

    expect($owner->can('create', [Animal::class, $ownFarm]))->toBeTrue();
    expect($owner->can('create', [Animal::class, $otherFarm]))->toBeFalse();
});

test('vet cannot add animals, even to an assigned farm', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    expect($vet->can('create', [Animal::class, $farm]))->toBeFalse();
});

test('owner can change the status of own animal, vet cannot', function () {
    $owner = User::factory()->create();
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);
    $animal = Animal::factory()->create(['farm_id' => $farm->id]);

    expect($owner->can('update', $animal))->toBeTrue();
    expect($vet->can('update', $animal))->toBeFalse();
});

test('vet sees animals only on assigned farms', function () {
    $vet = User::factory()->vet()->create();
    $assignedFarm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $assignedFarm->id, 'vet_id' => $vet->id]);

    $animalOnAssignedFarm = Animal::factory()->create(['farm_id' => $assignedFarm->id]);
    $animalOnOtherFarm = Animal::factory()->create();

    expect($vet->can('view', $animalOnAssignedFarm))->toBeTrue();
    expect($vet->can('view', $animalOnOtherFarm))->toBeFalse();
});
