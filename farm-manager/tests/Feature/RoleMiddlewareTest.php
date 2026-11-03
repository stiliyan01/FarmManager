<?php

use App\Models\Region;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:registrar,admin'])
        ->get('/test-role-page', fn () => 'ok');
});

test('users with an allowed role can open the page', function () {
    $registrar = User::factory()->registrar(Region::factory()->create())->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($registrar)->get('/test-role-page')->assertOk();
    $this->actingAs($admin)->get('/test-role-page')->assertOk();
});

test('users with another role get 403', function () {
    $owner = User::factory()->create();
    $vet = User::factory()->vet()->create();

    $this->actingAs($owner)->get('/test-role-page')->assertForbidden();
    $this->actingAs($vet)->get('/test-role-page')->assertForbidden();
});
