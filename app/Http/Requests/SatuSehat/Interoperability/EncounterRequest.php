<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EncounterRequest extends FormRequest
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
            'id' => $this->method() === 'PUT' ? ['required', 'string'] : '',
            'identifier' => ['required', 'array'],
            'identifier.*.system' => ['required', 'string'],
            'identifier.*.value' => ['required', 'string'],
            'identifier.*.use' => ['string'],
            'status_encounter' => ['required', 'string'],
            'class' => ['required', 'array'],
            'class.code' => ['required', 'string'],
            'class.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'participant' => ['required', 'array'],
            'participant.*.type' => ['required', 'array'],
            'participant.*.type.code' => ['required', 'string'],
            'participant.*.type.display' => ['string'],
            'participant.*.individual' => ['required', 'array'],
            'participant.*.individual.reference_id_practitioner' => ['required', 'string'],
            'participant.individual.display' => ['string'],
            'period' => ['required', 'array'],
            'period.start' => ['required', 'date'],
            'period.end' => ['date'],
            'location' => ['required', 'array'],
            'location.*.location' => ['required', 'array'],
            'location.*.location.reference_id_location' => ['required', 'string'],
            'location.*.location.display' => ['string'],
            'statusHistory' => ['required', 'array'],
            'statusHistory.*.status' => ['required', 'string'],
            'statusHistory.*.period' => ['array', 'required'],
            'statusHistory.*.period.start' => ['required', 'date'],
            'statusHistory.*.period.end' => ['date'],
            'serviceProvider' => ['required', 'array'],
            'serviceProvider.reference_id_organization' => ['required', 'string'],
            'serviceProvider.display' => ['string'],
            'diagnosis' => ['array'],
            'diagnosis.*.condition' => $this->filled('diagnosis') ? ['required', 'array'] : '',
            'diagnois.*.condition.reference_id_condition' => $this->filled('diagnosis.condition') ? ['required', 'string'] : '',
            'diagnois.*.condition.display' => ['string'],
            'diagnosis.*.use' => ['array'],
            'diagnois.*.use.code' => $this->filled('diagnosis.*.use') ? ['required', 'string'] : '',
            'diagnois.*.use.display' => ['string'],
            'diagnosis.rank' => ['numeric'],
            'basedOn' => ['array'],
            'basedOn.*.reference_id_service_request' => $this->filled('basedOn') ? ['required', 'string'] : '',
            'hospitalization' => ['array'],
            'hospitalization.dischargeDisposition' => $this->filled('hospitalization') ? ['required', 'array'] : '',
            'hospitalization.dischargeDisposition.code' => $this->filled('hospitalization.dischargeDisposition') ? ['required', 'string'] : '',
            'hospitalization.dischargeDisposition.display' => ['string'],
            'hospitalization.dischargeDisposition.text' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
