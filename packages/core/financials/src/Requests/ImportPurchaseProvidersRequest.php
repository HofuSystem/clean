<?php

namespace Core\Financials\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPurchaseProvidersRequest extends FormRequest
{
    use ApiResponse;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "data.*.name" => ['required', 'string'],
            "data.*.commercial_registration" => ['nullable', 'string'],
            "data.*.tax_number" => ['nullable', 'string'],
            "data.*.street_name" => ['nullable', 'string'],
            "data.*.building_no" => ['nullable', 'string'],
            "data.*.city_id" => ['nullable', 'exists:cities,id'],
            "data.*.district_id" => ['nullable', 'exists:districts,id'],
            "data.*.postal_code" => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            "data.*.name.required" => trans('Name is required'),
            "data.*.name.string" => trans('Name must be a string'),
            "data.*.city_id.exists" => trans('city is not Valid'),
            "data.*.district_id.exists" => trans('district is not Valid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
