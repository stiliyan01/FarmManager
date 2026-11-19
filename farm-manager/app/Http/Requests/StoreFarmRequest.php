<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Farm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Farm::class);
    }

    public function rules(): array
    {
        return [
            // Регистраторът записва фермата в своята област, администраторът избира областта.
            'region_id' => [Rule::requiredIf($this->user()->role === Role::Admin), 'nullable', 'exists:regions,id'],
            'reg_number' => ['required', 'string', 'max:20', 'unique:farms,reg_number'],
            'name' => ['required', 'string', 'max:255'],
            'settlement' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            // Ако е попълнен, на собственика веднага се изпраща покана.
            'owner_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
