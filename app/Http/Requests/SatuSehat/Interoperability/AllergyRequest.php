<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AllergyRequest extends FormRequest
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
            'identifier.*.system' => $this->filled('identifier') ? ['required', 'string'] : '',
            'identifier.*.value' => $this->filled('identifier') ? ['required', 'string'] : '',
            'identifier.*.use' => ['string'],
            'clinicalStatus' => ['array'],
            'clinicalStatus.code' => $this->filled('clinicalStatus') ? ['required', 'string'] : '',
            'clinicalStatus.display' => ['string'],
            'verificationStatus' => ['array'],
            'verificationStatus.code' => $this->filled('verificationStatus') ? ['required', 'string'] : '',
            'verificationStatus.display' => ['string'],
            'category' => ['required', 'array'],
            'category.*' => ['required', 'string'],
            'code' => ['required', 'array'],
            'code.system' => ['required', 'string'],
            'code.code' => ['required', 'string'],
            'code.display' => ['string'],
            'patient' => ['required', 'array'],
            'patient.reference_id_patient' => ['required', 'string'],
            'patient.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'recordedDate' => 'date',
            'recorder' => ['array'],
            'recorder.reference_id_practitioner' => $this->filled('recorder') ? ['required', 'string'] : '',
            'recorder.display' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}