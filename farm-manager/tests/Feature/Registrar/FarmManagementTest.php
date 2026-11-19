<?php

use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Invitation;
use App\Models\Region;
use App\Models\User;
use App\Notifications\FarmInvitation;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->shumen = Region::factory()->create(['name' => 'Шумен']);
    $this->registrar = User::factory()->registrar($this->shumen)->create();
});

test('registrar sees only the farms in own region', function () {
    Farm::factory()->count(2)->create(['region_id' => $this->shumen->id]);
    Farm::factory()->create();

    $this->actingAs($this->registrar)
        ->get(route('farms.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('farms/index')
            ->where('farms.total', 2)
            ->where('canCreate', true)
        );
});

test('farms without a vet can be filtered', function () {
    $farmWithVet = Farm::factory()->create(['region_id' => $this->shumen->id]);
    FarmVet::factory()->create(['farm_id' => $farmWithVet->id]);
    $farmWithoutVet = Farm::factory()->create(['region_id' => $this->shumen->id]);

    $this->actingAs($this->registrar)
        ->get(route('farms.index', ['filter' => 'without_vet']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('farms.total', 1)
            ->where('farms.data.0.id', $farmWithoutVet->id)
            ->where('farms.data.0.status', 'without_vet')
        );
});

test('registrar registers a farm in own region and the owner gets an invitation', function () {
    Notification::fake();

    $this->actingAs($this->registrar)
        ->post(route('farms.store'), [
            'reg_number' => '9700-0999',
            'name' => 'Ферма Лозево',
            'settlement' => 'с. Лозево',
            'capacity' => 40,
            'owner_email' => 'nov.sobstvenik@example.com',
        ])
        ->assertSessionHasNoErrors();

    $farm = Farm::where('reg_number', '9700-0999')->first();
    expect($farm->region_id)->toBe($this->shumen->id);
    expect($farm->owner_id)->toBeNull();

    $invitation = Invitation::where('farm_id', $farm->id)->first();
    expect($invitation->email)->toBe('nov.sobstvenik@example.com');
    expect($invitation->status())->toBe('pending');

    Notification::assertSentOnDemand(FarmInvitation::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'nov.sobstvenik@example.com';
    });
});

test('changing the vet closes the current assignment and keeps the history', function () {
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id]);
    $oldVet = User::factory()->vet()->create();
    $newVet = User::factory()->vet()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $oldVet->id, 'valid_from' => today()->subYear()]);

    $this->actingAs($this->registrar)
        ->post(route('farms.vets.store', $farm), ['vet_id' => $newVet->id, 'valid_from' => today()->toDateString()])
        ->assertSessionHasNoErrors();

    $oldAssignment = FarmVet::where('vet_id', $oldVet->id)->first();
    expect($oldAssignment->valid_to->toDateString())->toBe(today()->subDay()->toDateString());

    expect(FarmVet::where('farm_id', $farm->id)->count())->toBe(2);
    expect($newVet->isActiveVetFor($farm))->toBeTrue();
    expect($oldVet->isActiveVetFor($farm))->toBeFalse();
});

test('the same vet cannot be assigned again and the date must be after the current assignment', function () {
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id]);
    $vet = User::factory()->vet()->create();
    $otherVet = User::factory()->vet()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id, 'valid_from' => today()->subMonth()]);

    $this->actingAs($this->registrar)
        ->post(route('farms.vets.store', $farm), ['vet_id' => $vet->id, 'valid_from' => today()->toDateString()])
        ->assertSessionHasErrors('vet_id');

    $this->actingAs($this->registrar)
        ->post(route('farms.vets.store', $farm), ['vet_id' => $otherVet->id, 'valid_from' => today()->subMonths(2)->toDateString()])
        ->assertSessionHasErrors('valid_from');
});

test('registrar cannot manage a farm in another region', function () {
    $farmInOtherRegion = Farm::factory()->create();
    $vet = User::factory()->vet()->create();

    $this->actingAs($this->registrar)
        ->get(route('farms.edit', $farmInOtherRegion))
        ->assertForbidden();

    $this->actingAs($this->registrar)
        ->post(route('farms.vets.store', $farmInOtherRegion), ['vet_id' => $vet->id, 'valid_from' => today()->toDateString()])
        ->assertForbidden();
});

test('inspector, vet and owner cannot register farms', function () {
    $users = [
        User::factory()->inspector($this->shumen)->create(),
        User::factory()->vet()->create(),
        User::factory()->create(),
    ];

    foreach ($users as $user) {
        $this->actingAs($user)->get(route('farms.create'))->assertForbidden();
    }
});

test('registrar invites an owner to a farm without one', function () {
    Notification::fake();
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id, 'owner_id' => null]);

    $this->actingAs($this->registrar)
        ->post(route('farms.invitations.store', $farm), ['email' => 'owner@example.com'])
        ->assertSessionHasNoErrors();

    expect($farm->invitations()->count())->toBe(1);
    Notification::assertSentOnDemand(FarmInvitation::class);
});

test('a farm that already has an owner gets no invitation', function () {
    Notification::fake();
    $farm = Farm::factory()->create(['region_id' => $this->shumen->id]);

    $this->actingAs($this->registrar)
        ->post(route('farms.invitations.store', $farm), ['email' => 'owner@example.com'])
        ->assertSessionHasErrors('email');

    Notification::assertNothingSent();
});
