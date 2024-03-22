<?php

namespace App\Http\Requests\SatuSehat\Interoperability;

use Illuminate\Foundation\Http\FormRequest;

class ImagingStudyRequest extends FormRequest
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
            'identifier.*.system' => ['required_unless:identifier,null', 'string'],
            'identifier.*.value' => ['required_unless:identifier,null', 'string'],
            'identifier.*.type' => ['array'],
            'identifier.*.type.code' => ['required_unless:identifier.*.type,null', 'string'],
            'identifier.*.type.display' => ['string'],
            'identifier.*.use' => ['string'],
            'status_imaging_study' => ['required', 'string'],
            'modality' => ['required', 'array'],
            'modality.*.system' => ['required', 'string'],
            'modality.*.code' => ['required', 'string'],
            'modality.*.display' => ['string'],
            'subject' => ['required', 'array'],
            'subject.reference_id_patient' => ['required', 'string'],
            'subject.display' => ['string'],
            'started' => ['date'],
            'basedOn' => ['array'],
            'basedOn.*.reference_id_service_request' => ['required_unless:basedOn,null', 'string'],
            'numberOfSeries' => ['numeric'],
            'numberOfInstances' => ['numeric'],
            'series' => ['array'],
            'series.*.uid' => ['required_unless:series,null', 'string'],
            'series.*.number' => ['required_unless:series,null', 'numeric'],
            'series.*.modality' => ['required_unless:series,null', 'array'],
            'series.*.modality.system' => ['required_unless:series.*.modality,null', 'string'],
            'series.*.modality.code' => ['required_unless:series.*.modality,null', 'string'],
            'series.*.modality.display' => ['string'],
            'series.*.numberOfInstances' => ['numeric'],
            'series.*.started' => ['date'],
            'series.*.instance' => ['array'],
            'series.*.instance.*.uid' => ['required_unless:series.*.instance,null', 'string'],
            'series.*.instance.*.sopClass' => ['array'],
            'series.*.instance.*.sopClass.system' => ['required_unless:series.*.instance.*.sopClass,null', 'string'],
            'series.*.instance.*.sopClass.code' => ['required_unless:series.*.instance.*.sopClass,null', 'string'],
            'series.*.instance.*.sopClass.display' => ['string'],
            'series.*.instance.*.number' => ['numeric'],
            'series.*.instance.*.title' => ['string']

        ];
    }
}
