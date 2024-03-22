<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationDispenseRequest extends FormRequest
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
        if ($this->method() === 'GET') {
            return [];
        } elseif ($this->method() === 'POST') {
            return [
                'identifier' => ['required', 'array'],
                'identifier.*.system' => ['required', 'string'],
                'identifier.*.value' => ['required', 'string'],
                'identifier.*.use' => ['string'],
                'status_medication_dispense' => ['required', 'string'],
                'category' => ['required', 'array'],
                'category.code' => ['required', 'string'],
                'category.display' => ['string'],
                'priority' => 'string',
                'medicationReference' => ['required', 'array'],
                'medicationReference.reference_id_medication' => ['required', 'string'],
                'medicationReference.display' => ['string'],
                'subject' => ['required', 'array'],
                'subject.reference_id_patient' => ['required', 'string'],
                'subject.display' => ['string'],
                'context' => ['required', 'array'],
                'context.reference_id_encounter' => ['required', 'string'],
                'performer' => ['required', 'array'],
                'performer.*.actor' => $this->filled('performer') ? ['required', 'array'] : '',
                'performer.*.actor.reference_id_practitioner' => ['string'],
                'performer.*.actor.display' => ['string'],
                'location' => ['array'],
                'location.reference_id_location' => $this->filled('location') ? ['required', 'string'] : '',
                'location.display' => ['string'],
                'authorizingPrescription' => ['array'],
                'authorizingPrescription.*.reference_id_medication_request' => $this->filled('authorizingPrescription') ? ['required', 'string'] : '',
                'authorizingPrescription.*.display' => ['string'],
                'quantity' => ['array'],
                'quantity.value' => $this->filled('quantity') ? ['required', 'numeric'] : '',
                'quantity.code' => $this->filled('quantity') ? ['required', 'string'] : '',
                'daysSupply' => ['array'],
                'daysSupply.value' => $this->filled('daysSupply') ? ['required', 'numeric'] : '',
                'daysSupply.code' => $this->filled('daysSupply') ? ['required', 'string'] : '',
                'daysSupply.unit' => $this->filled('daysSupply') ? ['required', 'string'] : '',
                'whenPrepared' => ['date'],
                'whenHandedOver' => ['date'],
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
                'dosageInstruction.*.route.code' => ['string'],
                'dosageInstruction.*.route.display' => ['string'],
                'dosageInstruction.*.doseAndRate' => ['array'],
                'dosageInstruction.*.doseAndRate.*.type' => ['array'],
                'dosageInstruction.*.doseAndRate.*.type.code' => $this->filled('dosageInstruction.*.doseAndRate.*.type') ? ['required', 'string'] : '',
                'dosageInstruction.*.doseAndRate.*.type.display' => ['string'],
                'dosageInstruction.*.doseAndRate.*.doseQuantity' => ['array'],
                'dosageInstruction.*.doseAndRate.*.doseQuantity.value' => $this->filled('dosageInstruction.*.doseAndRate.*.doseQuantity') ? ['required', 'numeric'] : '',
                'dosageInstruction.*.doseAndRate.*.doseQuantity.code' => $this->filled('dosageInstruction.*.doseAndRate.*.doseQuantity') ? ['required', 'string'] : '',
                'dosageInstruction.*.doseAndRate.*.doseQuantity.unit' => ['string'],
            ];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
