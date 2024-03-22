<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationRequest extends FormRequest
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
            'code' => ['array'],
            'code.code' => $this->filled('code') ? ['required', 'string'] : '',
            'code.display' => ['string'],
            'status_medication' => ['string'],
            'manufacturer' => ['array'],
            'manufacturer.reference_id_organization' => $this->filled('manufacturer') ? ['required', 'string'] : '',
            'manufacturer.display' => ['string'],
            'form' => 'array',
            'form.code' => $this->filled('form') ? ['required', 'string'] : '',
            'form.display' => 'string',
            'ingredient' => 'array',
            'ingredient.*.itemCodeableConcept' => 'array',
            'ingredient.*.itemCodeableConcept.code' => $this->filled('ingredient.*.itemCodeableConcept') ? ['required', 'string'] : '',
            'ingredient.*.itemCodeableConcept.display' => ['string'],
            'ingredient.*.isActive' => 'bool',
            'ingredient.*.strength' => 'array',
            'ingredient.*.strength.numerator' => 'array',
            'ingredient.*.strength.numerator.code' => $this->filled('ingredient.*.strength.numerator') ? ['required', 'string'] : '',
            'ingredient.*.strength.numerator.value' => $this->filled('ingredient.*.strength.numerator') ? ['required', 'numeric'] : '',
            'ingredient.*.strength.denominator' => 'array',
            'ingredient.*.strength.denominator.code' => $this->filled('ingredient.*.strength.denominator') ? ['required', 'string'] : '',
            'ingredient.*.strength.denominator.value' => $this->filled('ingredient.*.strength.denominator') ? ['required', 'numeric'] : '',
            'extension' => 'array',
            'extension.*.url' => $this->filled('extension') ? ['required', 'string'] : '',
            'extension.*.valueCodeableConcept' => 'array',
            'extension.*.valueCodeableConcept.code' => $this->filled('extension.valueCodeableConcept') ? ['required', 'string'] : '',
            'extension.*.valueCodeableConcept.display' => ['string'],
            'batch' => 'array',
            'batch.lotNumber' => $this->filled('batch') ? ['required', 'string'] : '',
            'batch.expirationDate' => $this->filled('batch') ? ['required', 'date'] : '',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
