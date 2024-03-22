<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConditionRequest extends FormRequest
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
            'clinicalStatus' => ['array'],
            'clinicalStatus.code' => $this->filled('clinicalStatus') ? ['required', 'string'] : '',
            'clinicalStatus.display' => ['string'],
            'category' => ['required', 'array'],
            'category.code' => ['required', 'string'],
            'category.display' => ['string'],
            'code' => ['required', 'array'],
            'code.system' => ['required', 'string'],
            'code.code' => ['required', 'string'],
            'code.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'onsetDateTime' => 'date',
            'recordedDate' => 'date',
            'onsetRange' => ['array'],
            'onsetRange.low' => ['array'],
            'onsetRange.low.value' => $this->filled('onsetRange.low') ? ['required', 'numeric'] : '',
            'onsetRange.low.unit' => ['string'],
            'onsetRange.low.code' => $this->filled('onsetRange.low') ? ['required', 'string'] : '',
            'onsetRange.high' => ['array'],
            'onsetRange.high.value' => $this->filled('onsetRange.high') ? ['required', 'numeric'] : '',
            'onsetRange.high.unit' => ['string'],
            'onsetRange.high.code' => $this->filled('onsetRange.high') ? ['required', 'string'] : '',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}