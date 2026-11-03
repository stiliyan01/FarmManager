<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnimalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('animal'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:sold,dead'],
            'status_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
