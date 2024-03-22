<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompositionRequest extends FormRequest
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
            'identifier' => ['array'],
            'identifier.system' => $this->filled('identifier') ? ['required', 'string'] : '',
            'identifier.value' => $this->filled('identifier') ? ['required', 'string'] : '',
            'identifier.use' => ['string'],
            'status_composition' => ['required', 'string'],
            'type' => ['required', 'array'],
            'type.code' => ['required', 'string'],
            'type.display' => ['string'],
            'category' => ['required', 'array'],
            'category.code' => ['required', 'string'],
            'category.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'encounter.display' => ['string'],
            'date' => ['required', 'date'],
            'author' => ['required', 'array'],
            'author.*.reference_id_practitioner' => ['required', 'string'],
            'author.*.display' => ['string'],
            'title' => ['required', 'string'],
            'custodian' => ['array'],
            'custodian.reference_id_organization' => $this->filled('custodian') ? ['required', 'string'] : '',
            'section' => ['array'],
            'section.*.code' => ['array'],
            'section.*.code.code' => $this->filled('section.*.code') ? ['string'] : '',
            'section.*.code.display' => ['string'],
            'section.*.text' => ['array'],
            'section.*.text.status' => $this->filled('section.*.code') ? ['string'] : '',
            'section.*.text.div' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}