<?php

namespace Core\Financials\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPurchaseItemsRequest extends FormRequest
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
        ];
    }

    public function messages()
    {
        return [
            "data.*.name.required" => trans('Name is required'),
            "data.*.name.string" => trans('Name must be a string'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
