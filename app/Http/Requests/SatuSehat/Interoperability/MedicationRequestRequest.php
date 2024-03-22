<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationRequestRequest extends FormRequest
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
            'identifier' => ['required', 'array'],
            'identifier.*.system' => ['required', 'string'],
            'identifier.*.value' => ['required', 'string'],
            'identifier.*.use' => ['string'],
            'status_medication_request' => ['required', 'string'],
            'intent' => ['required', 'string'],
            'category' => ['required', 'array'],
            'category.*.code' => ['required', 'string'],
            'category.*.display' => ['string'],
            'priority' => 'string',
            'medicationReference' => ['required', 'array'],
            'medicationReference.reference_id_medication' => ['required', 'string'],
            'medicationReference.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'authoredOn' => ['date'],
            'requester' => ['array'],
            'requester.reference_id_practitioner' => $this->filled('requester') ? ['required', 'string'] : '',
            'requester.display' => ['string'],
            'reasonCode' => ['array'],
            'reasonCode.code' => $this->filled('reasonCode') ? ['required', 'string'] : '',
            'reasonCode.display' => ['string'],
            'courseOfTherapyType' => ['array'],
            'courseOfTherapyType.code' => $this->filled('courseOfTherapyType') ? ['required', 'string'] : '',
            'courseOfTherapyType.display' => ['string'],
            'dosageInstruction' => ['required', 'array'],
            'dosageInstruction.*.sequence' => ['required', 'numeric'],
            'dosageInstruction.*.text' => ['string'],
            'dosageInstruction.*.additionalInstruction' => ['array'],
            'dosageInstruction.*.additionalInstruction.*.text' => ['string'],
            'dosageInstruction.*.patientInstruction' => ['string'],
            'dosageInstruction.*.timing' => ['required', 'array'],
            'dosageInstruction.*.timing.repeat' => ['required', 'array'],
            'dosageInstruction.*.timing.repeat.frequency' => ['required', 'numeric'],
            'dosageInstruction.*.timing.repeat.period' => ['required', 'numeric'],
            'dosageInstruction.*.timing.repeat.periodUnit' => ['string'],
            'dosageInstruction.*.route' => ['array'],
            'dosageInstruction.*.route.code' => $this->filled('dosageInstruction.*.route') ? ['required', 'string'] : '',
            'dosageInstruction.*.route.display' => ['string'],
            'dosageInstruction.*.doseAndRate' => ['array'],
            'dosageInstruction.*.doseAndRate.*.type' => ['array'],
            'dosageInstruction.*.doseAndRate.*.type.code' => $this->filled('dosageInstruction.*.doseAndRate.*.type') ? ['required', 'string'] : '',
            'dosageInstruction.*.doseAndRate.*.type.display' => ['string'],
            'dosageInstruction.*.doseAndRate.*.doseQuantity' => ['array'],
            'dosageInstruction.*.doseAndRate.*.doseQuantity.value' => $this->filled('dosageInstruction.*.doseAndRate.*.doseQuantity') ? ['required', 'numeric'] : '',
            'dosageInstruction.*.doseAndRate.*.doseQuantity.code' => $this->filled('dosageInstruction.*.doseAndRate.*.doseQuantity') ? ['required', 'string'] : '',
            'dosageInstruction.*.doseAndRate.*.doseQuantity.unit' => ['string'],
            'dispenseRequest' => ['array'],
            'dispenseRequest.dispenseInterval' => ['array'],
            'dispenseRequest.dispenseInterval.value' => $this->filled('dispenseRequest.dispenseInterval') ? ['required', 'numeric'] : '',
            'dispenseRequest.dispenseInterval.code' => $this->filled('dispenseRequest.dispenseInterval') ? ['required', 'string'] : '',
            'dispenseRequest.dispenseInterval.unit' => ['string'],
            'dispenseRequest.validityPeriod' => ['array'],
            'dispenseRequest.validityPeriod.start' => $this->filled('dispenseRequest.validityPeriod') ? ['required', 'date'] : '',
            'dispenseRequest.validityPeriod.end' => ['date'],
            'dispenseRequest.numberOfRepeatsAllowed' => ['numeric'],
            'dispenseRequest.quantity' => ['array'],
            'dispenseRequest.quantity.value' => $this->filled('dispenseRequest.quantity') ? ['required', 'numeric'] : '',
            'dispenseRequest.quantity.code' => $this->filled('dispenseRequest.quantity') ? ['required', 'string'] : '',
            'dispenseRequest.quantity.unit' => ['string'],
            'dispenseRequest.performer' => ['array'],
            'dispenseRequest.performer.reference_id_organization' => $this->filled('dispenseRequest.performer') ? ['required', 'string'] : '',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
