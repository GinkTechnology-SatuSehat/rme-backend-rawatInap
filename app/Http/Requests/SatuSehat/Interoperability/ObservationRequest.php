<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObservationRequest extends FormRequest
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
            'status_observation' => ['required', 'string'],
            'category' => ['required', 'array'],
            'category.*.code' => ['required', 'string'],
            'category.*.display' => ['string'],
            'code' => ['required', 'array'],
            'code.system' => ['required', 'string'],
            'code.code' => ['required', 'string'],
            'code.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'performer' => ['required', 'array'],
            'performer.*' => $this->filled('performer') ? ['required', 'array'] : '',
            'performer.*.reference_id_practitioner' => ['string'],
            'performer.*.reference_id_organization' => ['string'],
            'performer.*.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'effectiveDateTime' => 'date',
            'issued' => 'date',
            'valueQuantity' => ['array'],
            'valueQuantity.value' => ['numeric'],
            'valueQuantity.unit' => ['string'],
            'valueQuantity.system' => ['string'],
            'valueQuantity.code' => ['string'],
            'bodySite' => ['array'],
            'bodySite.code' => $this->filled('bodySite') ? ['required', 'string'] : '',
            'bodySite.display' => ['string'],
            'interpretation' => ['array'],
            'interpretation.*.code' => $this->filled('interpretation') ? ['required', 'string'] : '',
            'interpretation.*.display' => ['string'],
            'valuableCodeableConcept' => ['array'],
            'valuableCodeableConcept.code' => $this->filled('valuableCodeableConcept') ? ['required', 'string'] : '',
            'specimen' => 'array',
            'specimen.reference_id_specimen' => $this->filled('specimen') ? ['required', 'string'] : '',
            'specimen.display' => ['string'],
            'basedOn' => 'array',
            'basedOn.*' => $this->filled('basedOn') ? ['required', 'array'] : '',
            'basedOn.*.reference_id_service_request' => ['string'],
            'referenceRange'  => 'array',
            'referenceRange.*' => $this->filled('referenceRange') ? ['required', 'array'] : '',
            'referenceRange.*.high' => ['array'],
            'referenceRange.*.high.value' => ['numeric'],
            'referenceRange.*.high.unit' => ['string'],
            'referenceRange.*.high.system' => ['string'],
            'referenceRange.*.high.code' => ['string'],
            'referenceRange.*.low' => ['array'],
            'referenceRange.*.low.value' => ['numeric'],
            'referenceRange.*.low.unit' => ['string'],
            'referenceRange.*.low.system' => ['string'],
            'referenceRange.*.low.code' => ['string'],
            'referenceRange.*.text' => 'string',
            'valueString' => ['string'],
            'derivedFrom' => ['array'],
            'derivedFrom.*.reference_id_imaging_study' => ['requiredunless:derivedFrom,null', 'string'],
            'derivedFrom.*.display' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
