<?php

namespace App\Http\Requests\SatuSehat\OnBoarding;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PatientRequest extends FormRequest
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
                'identifier' => ['array'],
                'identifier.*.system' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.value' => $this->filled('identifier') ? ['required', 'string'] : '',
                'identifier.*.use' => ['string'],
                'status_patient' => ['required', 'bool'],
                'name' => ['required', 'array'],
                'name.use' => ['required', 'string'],
                'name.text' => ['required', 'string'],
                'name.family' => ['string'],
                'name.given' => ['string'],
                'name.prefix' => ['string'],
                'name.suffix' => ['string'],
                'telecom' => ['array'],
                'telecom.*.system' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.value' => $this->filled('telecom') ? ['required', 'string'] : '',
                'telecom.*.use' => ['string'],
                'gender' => ['required', 'string'],
                'birthDate' => ['required', 'date'],
                'deceasedBoolean' => ['required', 'bool'],
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
                'maritalStatus' => ['array'],
                'maritalStatus.code' => $this->filled('maritalStatus') ? ['required', 'string'] : '',
                'maritalStatus.display' => ['string'],
                'multipleBirthInteger' => ['required', 'integer'],
                'contact' => ['array'],
                'contact.*.relationship' => $this->filled('contact') ? ['required', 'array'] : '',
                'contact.*.relationship.*.code' => $this->filled('contact.*.relationship') ? ['required', 'string'] : '',
                'contact.*.relationship.*.display' => ['string'],
                'contact.*.name' => $this->filled('contact') ? ['required', 'array'] : '',
                'contact.*.name.use' => $this->filled('contact.*.name') ? ['required', 'string'] : '',
                'contact.*.name.text' => $this->filled('contact.*.name') ? ['required', 'string'] : '',
                'contact.*.telecom' => ['array'],
                'contact.*.telecom.*.system' => $this->filled('contact.*.telecom') ? ['required', 'string'] : '',
                'contact.*.telecom.*.value' => $this->filled('contact.*.telecom') ? ['required', 'string'] : '',
                'contact.*.telecom.*.use' => ['string'],
                'contact.*.address' => ['array'],
                'contact.*.address.*.use' => ['string'],
                'contact.*.address.*.type' => ['string'],
                'contact.*.address.*.line' => ['array'],
                'contact.*.address.*.line.*' => ['string'],
                'contact.*.address.*.city' => ['string'],
                'contact.*.address.*.postalCode' => ['string'],
                'contact.*.address.*.country' => ['string'],
                // 'contact.*.address.*.extension.province.valueCode' => ['string'],
                // 'contact.*.address.*.extension.city.valueCode' => ['string'],
                // 'contact.*.address.*.extension.district.valueCode' => ['string'],
                // 'contact.*.address.*.extension.village.valueCode' => ['string'],
                // 'contact.*.address.*.extension.rt.valueCode' => ['string'],
                // 'contact.*.address.*.extension.rw.valueCode' => ['string'],
                'communication' => ['array'],
                'communication.*.language' => $this->filled('communication') ? ['required', 'array'] : '',
                'communication.*.language.code' => $this->filled('communication.*.language') ? ['required', 'string'] : '',
                'communication.*.language.display' => ['string'],
                'communication.*.preferred' => $this->filled('communication') ? ['required', 'bool'] : '',
                // 'extension' => ['array'],
                // 'extension.*.valueAddress' => $this->filled('extension') ? ['required', 'array'] : '',
                // 'extension.valueAddress.city' => $this->filled('extension.*.valueAddress') ? ['required', 'string'] : '',
                // 'extension.valueAddress.country' => $this->filled('extension.*.valueAddress') ? ['required', 'string'] : '',
                // 'extension.*.valueCode' => $this->filled('extension') ? ['required', 'string'] : ''
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
