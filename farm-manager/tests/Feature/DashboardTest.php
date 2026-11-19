<?php

use App\Models\Region;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

// Собственик без ферма вижда съобщение, че ОДБХ още не е регистрирала фермата му.
test('owner without a farm can visit the dashboard', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/dashboard')->assertOk();
});

test('every role is sent to its own dashboard', function () {
    $region = Region::factory()->create();

    $this->actingAs(User::factory()->vet()->create())->get('/dashboard')->assertRedirect(route('vet.dashboard'));
    $this->actingAs(User::factory()->registrar($region)->create())->get('/dashboard')->assertRedirect(route('farms.index'));
    $this->actingAs(User::factory()->inspector($region)->create())->get('/dashboard')->assertRedirect(route('control'));
    $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertRedirect(route('users.index'));
});

test('the last login time is saved', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);

    expect($user->fresh()->last_login_at)->not->toBeNull();
});
