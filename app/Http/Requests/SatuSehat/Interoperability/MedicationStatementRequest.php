<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;

class MedicationStatementRequest extends FormRequest
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
            'status_medication_statement' => ['required', 'string'],
            'category' => ['array'],
            'category.code' => $this->filled('category') ? ['required', 'string'] : '',
            'category.display' => ['string'],
            'medicationReference' => ['array'],
            'medicationReference.reference_id_medication' => $this->filled('medicationReference') ? ['required', 'string'] : '',
            'medicationReference.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'context' => ['required', 'array'],
            'context.reference_id_encounter' => ['required', 'string'],
            'dosage' => ['array'],
            'dosage.text' => ['string'],
            'dosage.timing' => ['array'],
            'dosage.timing.repeat' => ['array'],
            'dosage.timing.repeat.frequency' => $this->filled('dosage.timing.repeat') ? ['required', 'numeric'] : '',
            'dosage.timing.repeat.period' => $this->filled('dosage.timing.repeat') ? ['required', 'numeric'] : '',
            'dosage.timing.repeat.periodUnit' => $this->filled('dosage.timing.repeat') ? ['required', 'string'] : '',
            'dateAsserted' => ['date'],
            'effectiveDateTime' => ['date'],
            'informationSource' => ['array'],
            'informationSource.reference_id_patient' => $this->filled('informationSource') ? ['required', 'string'] : '',
            'informationSource.display' => ['string'],
        ];
    }
}
