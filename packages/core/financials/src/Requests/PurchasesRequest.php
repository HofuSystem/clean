<?php

namespace Core\Financials\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PurchasesRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_id' => 'required|integer|exists:purchase_items,id',
            'provider_id' => 'required|integer|exists:purchase_providers,id',
            'value_before_tax' => 'required|numeric|min:0',
            'tax_value' => 'required|numeric|min:0',
            'value_after_tax' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'collection_date' => 'nullable|date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'item_id.required' => trans('Item is required'),
            'item_id.exists' => trans('Selected Item is invalid'),
            'provider_id.required' => trans('Provider is required'),
            'provider_id.exists' => trans('Selected Provider is invalid'),
            'value_before_tax.required' => trans('Value before tax is required'),
            'tax_value.required' => trans('Tax value is required'),
            'value_after_tax.required' => trans('Value after tax is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
