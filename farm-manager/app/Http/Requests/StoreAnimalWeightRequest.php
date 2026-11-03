<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalWeightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('animal'));
    }

    public function rules(): array
    {
        return [
            'weight_kg' => ['required', 'numeric', 'min:0.1', 'max:2000'],
            'measured_at' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
