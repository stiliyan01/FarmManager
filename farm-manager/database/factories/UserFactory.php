<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => Role::Owner,
            'phone' => fake()->numerify('08########'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function vet(): static
    {
        return $this->state(['role' => Role::Vet]);
    }

    public function registrar(Region $region): static
    {
        return $this->state([
            'role' => Role::Registrar,
            'region_id' => $region->id,
        ]);
    }

    public function inspector(Region $region): static
    {
        return $this->state([
            'role' => Role::Inspector,
            'region_id' => $region->id,
        ]);
    }

    public function admin(): static
    {
        return $this->state(['role' => Role::Admin]);
    }
}
