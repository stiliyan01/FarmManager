<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Достъп има само администраторът – проверява се от middleware-а на маршрутите.
class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(Role::class)],
            'region_id' => ['nullable', 'required_if:role,registrar,inspector', 'exists:regions,id'],
            // Празна парола = паролата не се сменя.
            'password' => ['nullable', Password::defaults()],
        ];
    }
}
