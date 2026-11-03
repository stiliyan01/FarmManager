<?php

namespace App\Http\Requests;

use App\Models\Animal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', [Animal::class, $this->route('farm')]);
    }

    public function rules(): array
    {
        $farm = $this->route('farm');

        return [
            'ear_tag' => ['required', 'string', 'max:20', 'unique:animals,ear_tag'],
            'species_id' => ['required', 'exists:species,id'],
            // Породата трябва да е от избрания вид.
            'breed_id' => ['nullable', Rule::exists('breeds', 'id')->where('species_id', $this->input('species_id'))],
            'sex' => ['required', 'in:female,male'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            // Майката трябва да е женско животно от същата ферма и същия вид.
            'mother_id' => [
                'nullable',
                Rule::exists('animals', 'id')
                    ->where('farm_id', $farm->id)
                    ->where('species_id', $this->input('species_id'))
                    ->where('sex', 'female'),
            ],
            'group_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
