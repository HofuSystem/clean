<?php

namespace Core\Coupons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class AttacheGiftsRequest extends FormRequest
{
    use ApiResponse;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "gift_id"           => ['required', 'exists:gifts,id'],
        ];

    }

    public function messages()
    {
        return [
            "gift_id.required" => trans('Gift ID is required'),
            "gift_id.exists"   => trans('Gift ID is invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
