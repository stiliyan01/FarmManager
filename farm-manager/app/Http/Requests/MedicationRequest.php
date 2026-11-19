<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Нов препарат и редакция на препарат – правилата са едни и същи.
class MedicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'active_substance' => ['nullable', 'string', 'max:255'],
            'withdrawal_milk_days' => ['required', 'integer', 'min:0', 'max:365'],
            'withdrawal_meat_days' => ['required', 'integer', 'min:0', 'max:365'],
        ];
    }
}
