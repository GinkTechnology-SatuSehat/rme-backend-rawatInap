<?php

namespace App\Http\Requests\SatuSehat\OnBoarding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocationRequest extends FormRequest
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
        if ($this->method() === 'PATCH' || $this->method() === 'GET') {
            return [];
        } elseif ($this->method() === 'POST' || $this->method() === 'PUT') {
            return [
                'id' => $this->method() === 'PUT' ? ['required', 'string'] : '',
                'identifier' => ['array'],
                'identifier.*.system' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.value' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.use' => ['string'],
                'status_location' => ['required', 'string'],
                'name' => ['required', 'string'],
                'description' => ['string'],
                'mode' => ['required', 'string'],
                'telecom' => ['array'],
                'telecom.*.system' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.value' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.use' => ['string'],
                'address' => ['array'],
                'address.use' => ['string'],
                'address.type' => ['string'],
                'address.line' => ['array'],
                'address.line.*' => ['string'],
                'address.city' => ['string'],
                'address.postalCode' => ['string'],
                'address.country' => ['string'],
                // 'address.*.extension.province.valueCode' => ['string'],
                // 'address.*.extension.city.valueCode' => ['string'],
                // 'address.*.extension.district.valueCode' => ['string'],
                // 'address.*.extension.village.valueCode' => ['string'],
                // 'address.*.extension.rt.valueCode' => ['string'],
                // 'address.*.extension.rw.valueCode' => ['string'],
                // 'extension' => ['array'],
                // 'extension.system' => $this->filled('extension') ? ['required', 'string'] : '',
                // 'extension.code' => $this->filled('extension') ? ['required', 'string'] : '',
                // 'extension.display' => ['string'],
                'physicalType' => ['array', 'required'],
                'physicalType.code' => ['required', 'string'],
                'physicalType.display' => ['string'],
                'position' => ['array'],
                'position.longitude' => $this->filled('position') ? ['required', 'decimal:0,15'] : '',
                'position.latitude' => $this->filled('position') ? ['required', 'decimal:0,15'] : '',
                'position.altitude' => $this->filled('position') ? ['required', 'decimal:0,15'] : '',
                'managingOrganization' => ['array', 'required'],
                'managingOrganization.reference_id_organization' => ['required', 'string'],
                'managingOrganization.display' => ['string'],
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
