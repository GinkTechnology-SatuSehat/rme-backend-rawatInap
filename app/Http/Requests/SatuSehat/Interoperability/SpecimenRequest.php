<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SpecimenRequest extends FormRequest
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
            'identifier.*.assigner' => ['array'],
            'identitifer.*.assigner.reference' => $this->filled('identifier.*.assigner') ? ['required', 'string'] : '',
            'status_specimen' => ['required', 'string'],
            'type' => ['required', 'array'],
            'type.code' => ['required', 'string'],
            'type.display' => ['string'],
            'collection' => ['array'],
            'collection.method' => $this->filled('collection') ? ['required', 'array'] : '',
            'collection.method.code' => $this->filled('collection.method') ? ['required', 'string'] : '',
            'collection.method.display' => ['string'],
            'collection.collectedDateTime' => 'date',
            'collection.quantity' => ['array'],
            'collection.quantity.value' => $this->filled('collection.quantity') ? ['required', 'numeric'] : '',
            'collection.quantity.unit' => $this->filled('collection.quantity') ? ['required', 'string'] : '',
            'collection.collector' => ['array'],
            'collection.collector.reference_id_practitioner' => $this->filled('collection.collector') ? ['required', 'string'] : '',
            'collection.collector.display' => ['string'],
            'collection.fastingStatusCodeableConcept' => ['array'],
            'collection.fastingStatusCodeableConcept.code' => $this->filled('collection.fastingStatusCodeableConcept') ? ['required', 'string'] : '',
            'collection.fastingStatusCodeableConcept.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'request' => ['required', 'array'],
            'request.*.reference_id_service_request' => ['required', 'string'],
            'receivedTime' => 'date',
            'extension' => ['array'],
            'extension.*.url' => $this->filled('extension') ? ['required', 'string'] : '',
            'extension.*.valueDateTime' => ['date'],
            'extension.*.valueContactDetail' => ['array'],
            'extension.*.valueContactDetail.name' => ['string'],
            'extension.*.valueContactDetail.telecom' => ['array'],
            'extension.*.valueContactDetail.telecom.*.system' => $this->filled('extension.*.valueContactDetail.telecom') ? ['required', 'string'] : '',
            'extension.*.valueContactDetail.telecom.*.value' => $this->filled('extension.*.valueContactDetail.telecom') ? ['required', 'string'] : '',
            'extension.*.valueReference' => ['array'],
            'extension.*.valueReference.reference_id_practitioner' => ['required_unless:extension.*.valueReference,null', 'string'],
            'extension.*.valueReference.display' => ['string'],
            'processing' => ['array'],
            'processing.*.timeDateTime' => ['date'],
            'condition' => ['array'],
            'condition.*.text' => ['string']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
