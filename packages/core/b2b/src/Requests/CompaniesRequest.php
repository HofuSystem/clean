<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CompaniesRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname'         => ['required', 'string', 'max:255'],
            'name_ar'          => ['nullable', 'string', 'max:255'],
            'name_en'          => ['nullable', 'string', 'max:255'],
            'street_name'      => ['nullable', 'string', 'max:255'],
            'building_no'      => ['nullable', 'string', 'max:255'],
            'district_id'      => ['nullable', 'exists:districts,id'],
            'postal_code'      => ['nullable', 'string', 'max:255'],
            'additional_number' => ['nullable', 'string', 'max:255'],
            'city_id'          => ['nullable', 'exists:cities,id'],
            'line_of_business' => ['nullable', 'string', 'max:255'],
            'email'            => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'avatar'           => ['nullable', 'string'],
            'owner_id'         => ['nullable', 'exists:users,id'],
            'commercial_registration' => ['nullable', 'string', 'max:255'],
            'tax_number'             => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required'         => trans('company name is required'),
            'fullname.string'           => trans('company name should be a string'),
            'line_of_business.string'   => trans('line of business should be a string'),
            'email.email'               => trans('email should be a valid email address'),
            'owner_id.exists'           => trans('selected owner is not valid'),
            'commercial_registration.string' => trans('commercial registration should be a string'),
            'tax_number.string'             => trans('tax number should be a string'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
