<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Достъп има само администраторът – проверява се от middleware-а на маршрутите.
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::enum(Role::class)],
            // Област има само служителят на ОДБХ.
            'region_id' => ['nullable', 'required_if:role,registrar,inspector', 'exists:regions,id'],
            'password' => ['required', Password::defaults()],
        ];
    }
}
