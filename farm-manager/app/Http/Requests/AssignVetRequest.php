<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignVetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', $this->route('farm'));
    }

    public function rules(): array
    {
        $vetRules = ['required', Rule::exists('users', 'id')->where('role', Role::Vet->value)];
        $dateRules = ['required', 'date', 'before_or_equal:today'];

        // При смяна: друг ветеринар и дата след началото на сегашното назначение.
        $currentAssignment = $this->route('farm')->activeVetAssignment;
        if ($currentAssignment !== null) {
            $vetRules[] = Rule::notIn([$currentAssignment->vet_id]);
            $dateRules[] = 'after:'.$currentAssignment->valid_from->toDateString();
        }

        return [
            'vet_id' => $vetRules,
            'valid_from' => $dateRules,
        ];
    }

    public function messages(): array
    {
        return [
            'vet_id.not_in' => 'Този ветеринар вече обслужва фермата.',
            'valid_from.after' => 'Датата трябва да е след началото на сегашното назначение.',
        ];
    }
}
