<?php

use App\Enums\Role;
use App\Models\Farm;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

// Покана за нова ферма без собственик.
function makeInvitation(array $attributes = []): Invitation
{
    $farm = Farm::factory()->create(['owner_id' => null]);

    return Invitation::create(array_merge([
        'farm_id' => $farm->id,
        'email' => 'nov@example.com',
        'role' => Role::Owner,
        'token' => Str::random(64),
        'expires_at' => now()->addDays(7),
    ], $attributes));
}

test('new owner creates an account from the invitation and becomes the farm owner', function () {
    $invitation = makeInvitation();

    $this->get(route('invitations.show', $invitation->token))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('invitations/accept')
            ->where('step', 'register')
            ->where('problem', null)
        );

    $this->post(route('invitations.accept', $invitation->token), [
        'name' => 'Нов Собственик',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('farms.show', $invitation->farm));

    $owner = User::where('email', 'nov@example.com')->first();

    expect($owner->role)->toBe(Role::Owner);
    expect($invitation->farm->fresh()->owner_id)->toBe($owner->id);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
    $this->assertAuthenticatedAs($owner);
});

test('a user who already has an account logs in and then accepts', function () {
    $user = User::factory()->create(['email' => 'nov@example.com']);
    $invitation = makeInvitation();

    $this->get(route('invitations.show', $invitation->token))
        ->assertInertia(fn (Assert $page) => $page->where('step', 'login'));

    $this->actingAs($user)
        ->post(route('invitations.accept', $invitation->token))
        ->assertRedirect(route('farms.show', $invitation->farm));

    expect($invitation->farm->fresh()->owner_id)->toBe($user->id);
});

test('an expired invitation cannot be accepted', function () {
    $invitation = makeInvitation(['expires_at' => now()->subDay()]);

    $this->post(route('invitations.accept', $invitation->token), [
        'name' => 'Нов Собственик',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('invitation');

    expect(User::where('email', 'nov@example.com')->exists())->toBeFalse();
});

test('an invitation for another email cannot be accepted', function () {
    $invitation = makeInvitation();
    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->post(route('invitations.accept', $invitation->token))
        ->assertSessionHasErrors('invitation');

    expect($invitation->farm->fresh()->owner_id)->toBeNull();
});
