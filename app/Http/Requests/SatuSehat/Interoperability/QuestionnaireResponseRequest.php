<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireResponseRequest extends FormRequest
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
            'questionnaire' => ['required', 'string'],
            'status_questionnaire' => ['required', 'string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'encounter' => ['required', 'array'],
            'encounter.reference_id_encounter' => ['required', 'string'],
            'authored' => 'date',
            'author' => ['required', 'array'],
            'author.reference_id_practitioner' => ['required', 'string'],
            'author.display' => ['string'],
            'source' => ['required', 'array'],
            'source.reference_id_patient' => ['required', 'string'],
            'source.display' => ['string'],
            'item' => ['required', 'array'],
            'item.*.linkId' => ['string'],
            'item.*.text' => ['string'],
            'item.*.item' => ['array'],
            'item.*.item.*.linkId' => ['string'],
            'item.*.item.*.text' => ['string'],
            'item.*.item.*.answer' => ['array'],
            'item.*.item.*.answer.*.valueCoding' => ['array'],
            'item.*.item.*.answer.*.valueCoding.code' => ['string', 'required_unless:item.*.item.*.answer.*.valueCoding,null'],
            'item.*.item.*.answer.*.valueCoding.display' => ['string'],
            'item.*.item.*.answer.*.valueBoolean' => ['boolean'],
        ];
    }
}
