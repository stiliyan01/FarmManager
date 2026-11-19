<?php

namespace App\Http\Requests;

use App\Enums\AnimalStatus;
use App\Enums\VetEventType;
use App\Models\VetEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVetEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [VetEvent::class, $this->route('farm')]);
    }

    public function rules(): array
    {
        $farm = $this->route('farm');

        return [
            'type' => ['required', Rule::enum(VetEventType::class)],
            'medication_id' => ['nullable', 'required_if:type,vaccination,treatment', 'exists:medications,id'],
            'dose' => ['nullable', 'string', 'max:100'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'animal_ids' => ['required', 'array', 'min:1'],
            // Правило 6: записи се правят само за активни животни от същата ферма.
            'animal_ids.*' => [
                Rule::exists('animals', 'id')
                    ->where('farm_id', $farm->id)
                    ->where('status', AnimalStatus::Active->value),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'medication_id.required_if' => 'При ваксинация и лечение изберете препарат.',
            'animal_ids.required' => 'Изберете поне едно животно.',
            'animal_ids.*.exists' => 'Записи се правят само за активни животни от тази ферма.',
        ];
    }
}
