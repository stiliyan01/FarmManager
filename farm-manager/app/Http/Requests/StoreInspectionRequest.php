<?php

namespace App\Http\Requests;

use App\Enums\InspectionResult;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Правото (инспектор от областта на фермата) се проверява в контролера, след като фермата е избрана.
class StoreInspectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'farm_id' => ['required', 'exists:farms,id'],
            'inspected_at' => ['required', 'date', 'before_or_equal:today'],
            'result' => ['required', Rule::enum(InspectionResult::class)],
            'findings' => ['nullable', 'required_if:result,violation', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'findings.required_if' => 'Опишете установеното нарушение.',
        ];
    }
}
