<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Нова схема и редакция на схема – правилата са едни и същи.
class VaccinationSchemeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'species_id' => ['required', 'exists:species,id'],
            'name' => ['required', 'string', 'max:255'],
            'medication_id' => ['required', 'exists:medications,id'],
            'interval_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'mandatory' => ['required', 'boolean'],
        ];
    }
}
