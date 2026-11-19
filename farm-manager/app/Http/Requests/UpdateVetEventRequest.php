<?php

namespace App\Http\Requests;

use App\Enums\VetEventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVetEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('vetEvent'));
    }

    // Животното не се сменя – само данните на записа.
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(VetEventType::class)],
            'medication_id' => ['nullable', 'required_if:type,vaccination,treatment', 'exists:medications,id'],
            'dose' => ['nullable', 'string', 'max:100'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'medication_id.required_if' => 'При ваксинация и лечение изберете препарат.',
        ];
    }
}
