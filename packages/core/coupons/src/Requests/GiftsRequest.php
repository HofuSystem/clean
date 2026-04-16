<?php

namespace Core\Coupons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class GiftsRequest extends FormRequest
{
    use ApiResponse;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "translations.en.title"  => ['required', 'string'],
            "translations.ar.title"  => ['required', 'string'],
            "translations.en.intro"  => ['nullable', 'string'],
            "translations.ar.intro"  => ['nullable', 'string'],
            "status"                 => ['required', 'in:active,not-active'],
            "from"                   => ['nullable', 'date'],
            "to"                     => ['nullable', 'date'],
            "coupon_code"            => ['nullable', 'string'],
            "order_type"             => ['nullable', 'in:clothes,sales,services,maid,host'],

            "register_from"          => ['nullable', 'date'],
            "register_to"            => ['nullable', 'date'],
            "orders_from"            => ['nullable', 'date'],
            "orders_to"              => ['nullable', 'date'],
            "orders_min"             => ['nullable', 'numeric'],
            "orders_max"             => ['nullable', 'numeric'],
            "type"                   => ['required', 'in:value,percentage'],
            "value"                  => ['required', 'numeric'],
            "max_value"              => ['nullable', 'numeric'],
        ];

    }

    public function messages()
    {
        return [
            "translations.en.title.required" => trans('Title in English is required'),
            "translations.ar.title.required" => trans('Title in Arabic is required'),
            "status.required"                => trans('Status is required'),
            "status.in"                      => trans('Status is invalid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
