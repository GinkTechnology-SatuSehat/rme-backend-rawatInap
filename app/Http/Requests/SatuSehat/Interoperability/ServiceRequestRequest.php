<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServiceRequestRequest extends FormRequest
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
            'status_service_request' => ['required', 'string'],
            'intent' => ['required', 'string'],
            'priority' => ['string'],
            'category' => ['array'],
            'category.*.code' => $this->filled('category') ? ['required', 'string'] : '',
            'category.*.display' => ['string'],
            'code' => ['required', 'array'],
            'code.system' => ['required', 'string'],
            'code.code' => ['required', 'string'],
            'code.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'requester' => ['required', 'array'],
            'requester.reference_id_practitioner' => ['required', 'string'],
            'performer' => ['required', 'array'],
            'performer.*.reference_id_practitioner' => ['required', 'string'],
            'performer.*.display' => ['string'],
            'occurrenceDateTime' => 'date',
            'authoredOn' => 'date',
            'reasonCode' => 'array',
            'reasonCode.*.text' => ['required', 'string'],
            'reasonReference' => ['array'],
            'reasonReference.*.Reference_id_condition' => $this->filled('reasonReference') ? ['required', 'string'] : '',
            'patientInstruction' => ['string'],
            'supportingInfo' => ['array'],
            'supportingInfo.*.reference_id_procedure' => $this->filled('supportingInfo') ? ['required', 'string'] : '',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
