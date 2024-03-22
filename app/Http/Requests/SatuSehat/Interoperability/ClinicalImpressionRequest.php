<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClinicalImpressionRequest extends FormRequest
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
            'status_clinical_impression' => ['required', 'string'],
            'description' => 'string',
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'encounter.display' => ['string'],
            'effectiveDateTime' => 'date',
            'date' => ['required', 'date'],
            'assessor' => ['array'],
            'assessor.reference_id_practitioner' => $this->filled('assesor') ? ['required', 'string'] : '',
            'assessor.display' => ['string'],
            'problem' => ['array'],
            'problem.*.reference_id_condition' => $this->filled('problem') ? ['required', 'string'] : '',
            'problem.*.display' => ['string'],
            'investigation' => ['array'],
            'investigation.*.code' => $this->filled('investigation') ? ['required', 'array'] : '',
            'investigation.*.code.text' => ['string'],
            'investigation.*.item' => $this->filled('investigation') ? ['required', 'array'] : '',
            'investigation.*.item.*.reference_id_observation' => $this->filled('investigation.*.item') ? ['required', 'string'] : '',
            'investigation.*.item.*.display' => ['string'],
            'summary' => 'string',
            'finding' => ['required', 'array'],
            'finding.*.itemCodeableConcept' => ['required', 'array'],
            'finding.*.itemCodeableConcept.code' => $this->filled('finding.*.itemCodeableConcept') ? ['string'] : '',
            'finding.*.itemCodeableConcept.display' => ['string'],
            'finding.*.itemReference' => ['required', 'array'],
            'finding.*.itemReference.reference_id_condition' => $this->filled('finding.*.itemReference') ? ['required', 'string'] : '',
            'prognosisCodeableConcept' => ['required', 'array'],
            'prognosisCodeableConcept.code' => $this->filled('prognosisCodeableConcept') ? ['required', 'string'] : '',
            'prognosisCodeableConcept.display' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
