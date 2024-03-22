<?php

namespace App\Http\Requests\SatuSehat\OnBoarding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubOrganizationRequest extends FormRequest
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
        if ($this->method() === 'GET' || $this->method() === 'PATCH') {
            return [];
        } else {
            return [
                'id' => $this->method() === 'PUT' ? ['required', 'string'] : '',
                'status_organization' => ['required', 'bool'],
                'identifier' => ['array'],
                'identifier.*.system' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.value' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.use' => ['string'],
                'type' => ['array', 'required'],
                'type.code' => ['required', 'string'],
                'type.display' => ['string'],
                'name' => ['required', 'string'],
                'telecom' => ['array'],
                'telecom.*.system' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.value' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.use' => ['string'],
                'address' => ['array'],
                'address.*.use' => ['string'],
                'address.*.type' => ['string'],
                'address.*.line' => ['array'],
                'address.*.line.*' => ['string'],
                'address.*.city' => ['string'],
                'address.*.postalCode' => ['string'],
                'address.*.country' => ['string'],
                // 'address.*.extension.province.valueCode' => ['string'],
                // 'address.*.extension.city.valueCode' => ['string'],
                // 'address.*.extension.district.valueCode' => ['string'],
                // 'address.*.extension.village.valueCode' => ['string'],
                // 'address.*.extension.rt.valueCode' => ['string'],
                // 'address.*.extension.rw.valueCode' => ['string'],
                'partOf' => ['array', 'required'],
                'partOf.reference_id_organization' => $this->filled('partOf') ? ['required', 'string'] : '',
                'partOf.display' => ['string'],
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
