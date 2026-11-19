<?php

use App\Enums\Role;
use App\Models\Animal;
use App\Models\Breed;
use App\Models\Farm;
use App\Models\FarmVet;
use App\Models\Medication;
use App\Models\Region;
use App\Models\Species;
use App\Models\User;
use App\Models\VaccinationScheme;
use App\Models\VetEvent;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->region = Region::factory()->create();
});

test('only the admin opens the admin pages', function () {
    $users = [
        User::factory()->create(),
        User::factory()->vet()->create(),
        User::factory()->registrar($this->region)->create(),
        User::factory()->inspector($this->region)->create(),
    ];

    foreach ($users as $user) {
        $this->actingAs($user)->get(route('users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('species.index'))->assertForbidden();
        $this->actingAs($user)->get(route('vaccination-schemes.index'))->assertForbidden();
        $this->actingAs($user)->get(route('activity-log.index'))->assertForbidden();
    }

    $this->actingAs($this->admin)->get(route('users.index'))->assertOk();
});

test('admin creates a registrar for a region', function () {
    $this->actingAs($this->admin)
        ->post(route('users.store'), [
            'name' => 'Нов Регистратор',
            'email' => 'nov.registrator@example.com',
            'phone' => null,
            'role' => 'registrar',
            'region_id' => $this->region->id,
            'password' => 'password',
        ])
        ->assertRedirect(route('users.index'));

    $user = User::where('email', 'nov.registrator@example.com')->first();
    expect($user->role)->toBe(Role::Registrar);
    expect($user->region_id)->toBe($this->region->id);
});

test('ODBH roles need a region, the other roles do not keep one', function () {
    $this->actingAs($this->admin)
        ->post(route('users.store'), [
            'name' => 'Инспектор',
            'email' => 'inspektor@example.com',
            'role' => 'inspector',
            'region_id' => null,
            'password' => 'password',
        ])
        ->assertSessionHasErrors('region_id');

    $this->actingAs($this->admin)->post(route('users.store'), [
        'name' => 'Ветеринар',
        'email' => 'vet@example.com',
        'role' => 'vet',
        'region_id' => $this->region->id,
        'password' => 'password',
    ]);

    expect(User::where('email', 'vet@example.com')->first()->region_id)->toBeNull();
});

test('the password stays the same when the field is left empty', function () {
    $user = User::factory()->create();

    $this->actingAs($this->admin)
        ->put(route('users.update', $user), [
            'name' => 'Ново Име',
            'email' => $user->email,
            'phone' => null,
            'role' => 'owner',
            'region_id' => null,
            'password' => null,
        ])
        ->assertSessionHasNoErrors();

    $user->refresh();
    expect($user->name)->toBe('Ново Име');
    expect(Hash::check('password', $user->password))->toBeTrue();
});

test('users can be filtered by role', function () {
    User::factory()->vet()->count(2)->create();

    $this->actingAs($this->admin)
        ->get(route('users.index', ['role' => 'vet']))
        ->assertInertia(fn (Assert $page) => $page->where('users.total', 2)->where('users.data.0.role', 'vet'));
});

test('admin adds a species and a breed and renames the breed', function () {
    $this->actingAs($this->admin)->post(route('species.store'), ['name' => 'Бивол'])->assertSessionHasNoErrors();
    $buffalo = Species::where('name', 'Бивол')->first();

    $this->actingAs($this->admin)->post(route('breeds.store', $buffalo), ['name' => 'Българска мурра'])->assertSessionHasNoErrors();
    $breed = Breed::where('species_id', $buffalo->id)->first();

    $this->actingAs($this->admin)->put(route('breeds.update', $breed), ['name' => 'Мурра'])->assertSessionHasNoErrors();
    expect($breed->fresh()->name)->toBe('Мурра');

    // Една и съща порода не може да се добави два пъти към вида.
    $this->actingAs($this->admin)->post(route('breeds.store', $buffalo), ['name' => 'Мурра'])->assertSessionHasErrors('name');
});

test('vet sees the medications, but only the admin changes them', function () {
    $vet = User::factory()->vet()->create();
    $medication = Medication::factory()->create(['name' => 'Пен-Стреп']);

    $this->actingAs($vet)
        ->get(route('medications.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('canManage', false));

    $this->actingAs($vet)->get(route('medications.create'))->assertForbidden();

    $this->actingAs($this->admin)
        ->put(route('medications.update', $medication), [
            'name' => 'Пен-Стреп',
            'active_substance' => 'пеницилин',
            'withdrawal_milk_days' => 4,
            'withdrawal_meat_days' => 30,
        ])
        ->assertRedirect(route('medications.index'));

    expect($medication->fresh()->withdrawal_meat_days)->toBe(30);
});

test('admin adds and deletes a vaccination scheme', function () {
    $sheep = Species::factory()->create(['name' => 'Овца']);
    $vaccine = Medication::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('vaccination-schemes.store'), [
            'species_id' => $sheep->id,
            'name' => 'Антракс',
            'medication_id' => $vaccine->id,
            'interval_days' => 365,
            'mandatory' => true,
        ])
        ->assertRedirect(route('vaccination-schemes.index'));

    $scheme = VaccinationScheme::first();
    expect($scheme->mandatory)->toBeTrue();

    $this->actingAs($this->admin)->delete(route('vaccination-schemes.destroy', $scheme));
    expect(VaccinationScheme::count())->toBe(0);
});

test('the activity log shows who changed a vet record and how', function () {
    $vet = User::factory()->vet()->create();
    $farm = Farm::factory()->create();
    FarmVet::factory()->create(['farm_id' => $farm->id, 'vet_id' => $vet->id]);
    $animal = Animal::factory()->create(['farm_id' => $farm->id]);
    $event = VetEvent::factory()->create(['animal_id' => $animal->id, 'vet_id' => $vet->id, 'dose' => '5 ml']);

    // Журналът взима автора от влезлия потребител.
    $this->actingAs($vet);
    $event->update(['dose' => '8 ml']);

    $this->actingAs($this->admin)
        ->get(route('activity-log.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('activities.data.0.user', $vet->name)
            ->where('activities.data.0.action', 'редактира ветеринарен запис №'.$event->id)
            ->where('activities.data.0.changes.0.field', 'доза')
            ->where('activities.data.0.changes.0.old', '5 ml')
            ->where('activities.data.0.changes.0.new', '8 ml')
        );
});
