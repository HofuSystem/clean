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
            'reference_id' => 'nullable|string|unique:purchases,reference_id,' . ($this->route('id') ?: 'NULL') . ',id',
            'item_id' => 'required|integer|exists:purchase_items,id',
            'provider_id' => 'required|integer|exists:purchase_providers,id',
            'value_before_tax' => 'required|numeric|min:0',
            'tax_value' => 'required|numeric|min:0',
            'value_after_tax' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'attachment' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (request()->hasFile('attachment')) {
                        $file = request()->file('attachment');
                        $ext = strtolower($file->getClientOriginalExtension());
                        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                        if (!in_array($ext, $allowed)) {
                            $fail(trans('The attachment must be a file of type: ') . implode(', ', $allowed) . '.');
                        }
                        if ($file->getSize() > 10240 * 1024) {
                            $fail(trans('The attachment must not be greater than 10MB.'));
                        }
                    } elseif (!is_string($value)) {
                        $fail(trans('The attachment must be a valid file or path.'));
                    }
                }
            ],
            'collection_date' => 'nullable|date',
            'bank_transfer_files' => 'nullable|string',
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
