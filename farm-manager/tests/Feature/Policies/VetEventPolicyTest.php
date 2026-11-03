<?php

use App\Models\Animal;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Region;
use App\Models\User;
use App\Models\VetEvent;

test('assigned vet can create, update and delete vet records', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    $animal = Animal::factory()->create(['farm_id' => $farm->id]);
    $event = VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $vet->id]);

    expect($vet->can('create', [VetEvent::class, $farm]))->toBeTrue();
    expect($vet->can('update', $event))->toBeTrue();
    expect($vet->can('delete', $event))->toBeTrue();
});

test('vet without an assignment cannot create vet records', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();

    expect($vet->can('create', [VetEvent::class, $farm]))->toBeFalse();
});

test('previous vet loses access, but the old records stay visible to owner and inspector', function () {
    $region = Region::factory()->create();
    $owner = User::factory()->create();
    $inspector = User::factory()->inspector($region)->create();
    $oldVet = User::factory()->vet()->create();
    $newVet = User::factory()->vet()->create();

    $farm = Farm::factory()->create(['region_id' => $region->id, 'owner_id' => $owner->id]);
    FarmVet::factory()->ended()->create(['farm_id' => $farm->id, 'vet_id' => $oldVet->id]);
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $newVet->id]);

    $animal = Animal::factory()->create(['farm_id' => $farm->id]);
    $oldRecord = VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $oldVet->id]);

    // Старият ветеринар вече няма достъп.
    expect($oldVet->can('view', $oldRecord))->toBeFalse();
    expect($oldVet->can('update', $oldRecord))->toBeFalse();
    expect($oldVet->can('create', [VetEvent::class, $farm]))->toBeFalse();

    // Записът остава видим за собственика, инспектора и новия ветеринар.
    expect($owner->can('view', $oldRecord))->toBeTrue();
    expect($inspector->can('view', $oldRecord))->toBeTrue();
    expect($newVet->can('view', $oldRecord))->toBeTrue();
});

test('owner, registrar and inspector cannot create or edit vet records', function () {
    $region = Region::factory()->create();
    $owner = User::factory()->create();
    $registrar = User::factory()->registrar($region)->create();
    $inspector = User::factory()->inspector($region)->create();

    $farm = Farm::factory()->create(['region_id' => $region->id, 'owner_id' => $owner->id]);
    $animal = Animal::factory()->create(['farm_id' => $farm->id]);
    $event = VetEvent::factory()->create(['animal_id' => $animal->id]);

    foreach ([$owner, $registrar, $inspector] as $user) {
        expect($user->can('create', [VetEvent::class, $farm]))->toBeFalse();
        expect($user->can('update', $event))->toBeFalse();
        expect($user->can('delete', $event))->toBeFalse();
    }
});
