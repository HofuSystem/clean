<?php

namespace Core\Financials\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPurchasesRequest extends FormRequest
{
    use ApiResponse;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "data.*.item_id" => ['required', 'exists:purchase_items,id'],
            "data.*.provider_id" => ['required', 'exists:purchase_providers,id'],
            "data.*.value_before_tax" => ['required', 'numeric'],
            "data.*.tax_value" => ['required', 'numeric'],
            "data.*.value_after_tax" => ['required', 'numeric'],
            "data.*.notes" => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            "data.*.item_id.required" => trans('Item is required'),
            "data.*.item_id.exists" => trans('Selected Item is invalid'),
            "data.*.provider_id.required" => trans('Provider is required'),
            "data.*.provider_id.exists" => trans('Selected Provider is invalid'),
            "data.*.value_before_tax.required" => trans('Value before tax is required'),
            "data.*.tax_value.required" => trans('Tax value is required'),
            "data.*.value_after_tax.required" => trans('Value after tax is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
