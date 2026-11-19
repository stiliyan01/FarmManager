<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('farm'));
    }

    public function rules(): array
    {
        return [
            'reg_number' => ['required', 'string', 'max:20', Rule::unique('farms', 'reg_number')->ignore($this->route('farm'))],
            'name' => ['required', 'string', 'max:255'],
            'settlement' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ];
    }
}
