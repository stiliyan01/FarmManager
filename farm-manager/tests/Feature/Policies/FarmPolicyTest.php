<?php

use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Region;
use App\Models\User;

test('owner can view own farm', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);

    expect($owner->can('view', $farm))->toBeTrue();
});

test('owner cannot view another owner\'s farm', function () {
    $owner = User::factory()->create();
    $otherFarm = Farm::factory()->create();

    expect($owner->can('view', $otherFarm))->toBeFalse();
});

// Ключовото правило: ветеринарят вижда само фермите, към които е назначен.
test('vet can view a farm with an active assignment', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    expect($vet->can('view', $farm))->toBeTrue();
});

test('vet cannot view a farm without an assignment, even in the same region', function () {
    $region = Region::factory()->create();
    $vet = User::factory()->vet()->create();

    $assignedFarm = Farm::factory()->create(['region_id' => $region->id]);
    $neighbourFarm = Farm::factory()->create(['region_id' => $region->id]);
    FarmVet::factory()->create(['farm_id' => $assignedFarm->id, 'vet_id' => $vet->id]);

    expect($vet->can('view', $assignedFarm))->toBeTrue();
    expect($vet->can('view', $neighbourFarm))->toBeFalse();
});

test('vet loses access when the assignment is closed', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->ended()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    expect($vet->can('view', $farm))->toBeFalse();
});

test('registrar and inspector can view farms only in their own region', function () {
    $shumen = Region::factory()->create();
    $varna = Region::factory()->create();

    $registrar = User::factory()->registrar($shumen)->create();
    $inspector = User::factory()->inspector($shumen)->create();

    $farmInShumen = Farm::factory()->create(['region_id' => $shumen->id]);
    $farmInVarna = Farm::factory()->create(['region_id' => $varna->id]);

    expect($registrar->can('view', $farmInShumen))->toBeTrue();
    expect($registrar->can('view', $farmInVarna))->toBeFalse();

    expect($inspector->can('view', $farmInShumen))->toBeTrue();
    expect($inspector->can('view', $farmInVarna))->toBeFalse();
});

test('admin can view any farm', function () {
    $admin = User::factory()->admin()->create();
    $farm = Farm::factory()->create();

    expect($admin->can('view', $farm))->toBeTrue();
});

test('only registrar and admin can create farms', function () {
    $region = Region::factory()->create();

    expect(User::factory()->registrar($region)->create()->can('create', Farm::class))->toBeTrue();
    expect(User::factory()->admin()->create()->can('create', Farm::class))->toBeTrue();

    expect(User::factory()->create()->can('create', Farm::class))->toBeFalse();
    expect(User::factory()->vet()->create()->can('create', Farm::class))->toBeFalse();
    expect(User::factory()->inspector($region)->create()->can('create', Farm::class))->toBeFalse();
});

test('registrar can manage farms (invite owner, assign vet) only in own region', function () {
    $shumen = Region::factory()->create();
    $varna = Region::factory()->create();
    $registrar = User::factory()->registrar($shumen)->create();

    $farmInShumen = Farm::factory()->create(['region_id' => $shumen->id]);
    $farmInVarna = Farm::factory()->create(['region_id' => $varna->id]);

    expect($registrar->can('manage', $farmInShumen))->toBeTrue();
    expect($registrar->can('manage', $farmInVarna))->toBeFalse();
});

test('inspector, vet and owner cannot manage farms', function () {
    $region = Region::factory()->create();
    $owner = User::factory()->create();
    $vet = User::factory()->vet()->create();
    $inspector = User::factory()->inspector($region)->create();

    $farm = Farm::factory()->create(['region_id' => $region->id, 'owner_id' => $owner->id]);
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);

    expect($owner->can('manage', $farm))->toBeFalse();
    expect($vet->can('manage', $farm))->toBeFalse();
    expect($inspector->can('manage', $farm))->toBeFalse();
});
