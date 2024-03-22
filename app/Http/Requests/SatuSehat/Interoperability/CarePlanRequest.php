<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;

class CarePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'status_care_plan' => ['required', 'string'],
            'category' => ['required', 'array'],
            'category.*.code' => ['required', 'string'],
            'category.*.display' => ['string'],
            'intent' => ['required', 'string'],
            'description' => ['required', 'string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'created' => 'date',
            'author' => ['required', 'array'],
            'author.reference_id_practitioner' => ['required', 'string'],
        ];
    }
}
