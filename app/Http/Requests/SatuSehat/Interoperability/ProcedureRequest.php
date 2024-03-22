<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcedureRequest extends FormRequest
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
            'status_procedure' => ['required', 'string'],
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
            'performer' => ['array'],
            'performer.*.actor' => $this->filled('performer') ? ['required', 'array'] : '',
            'performer.*.actor.reference_id_practitioner' => ['string'],
            'performer.*.actor.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'encounter.display' => ['string'],
            'performedPeriod' => ['array'],
            'performedPeriod.start' => $this->filled('performedPeriod') ? ['required', 'date'] : '',
            'performedPeriod.end' => ['date'],
            'reasonCode' => ['array'],
            'reasonCode.code' => $this->filled('reasonCode') ? ['required', 'string'] : '',
            'reasonCode.display' => ['string'],
            'bodySite' => ['array'],
            'bodySite.code' => $this->filled('bodySite') ? ['required', 'string'] : '',
            'bodySite.display' => ['string'],
            'note' => ['array'],
            'note.*' => $this->filled('note') ? ['required', 'array'] : '',
            'note.*.text' => ['string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}