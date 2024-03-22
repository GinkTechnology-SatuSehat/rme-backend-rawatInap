<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiagnosticReportRequest extends FormRequest
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
            'status_diagnostic_report' => ['required', 'string'],
            'category' => ['required', 'array'],
            'category.*.code' => ['required', 'string'],
            'category.*.display' => ['string'],
            'code' => ['required', 'array'],
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
            'result' => ['required', 'array'],
            'result.*' => $this->filled('result') ? ['required', 'array'] : '',
            'result.*.reference_id_observation' => ['string'],
            'result.*.display' => ['string'],
            'specimen' => ['array'],
            'specimen.*' => $this->filled('specimen') ? ['required', 'array'] : '',
            'specimen.*.reference_id_spesimen' => ['string'],
            'specimen.*.display' => ['string'],
            'imaging_study' => ['array'],
            'imaging_study.*' => $this->filled('imaging_study') ? ['required', 'array'] : '',
            'imaging_study.*.reference_id_imaging_study' => ['string'],
            'imaging_study.*.display' => ['string'],
            'basedOn' => ['required', 'array'],
            'basedOn.*' => $this->filled('basedOn') ? ['required', 'array'] : '',
            'basedOn.*.reference_id_service_request' => ['string'],
            'conclusionCode' => ['array'],
            'conclusionCode.*' => $this->filled('conclusionCode') ? ['required', 'array'] : '',
            'conclusionCode.*.system' => ['required', 'string'],
            'conclusionCode.*.code' => ['required', 'string'],
            'conclusionCode.*.display' => ['string'],
            'conclusion' => ['string']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
