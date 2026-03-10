<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrdersRequest extends FormRequest
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
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric',
            'items.*.width'       => 'nullable|numeric',
            'items.*.height'      => 'nullable|numeric',
            'city_id'             => 'required|exists:cities,id',
            'district_id'         => 'required|exists:districts,id',
            'client_id'           => 'required|exists:users,id',
            'coupon_id'           => 'nullable|exists:coupons,id',
            'desc'                => 'nullable|string',
            'pay_type'            => 'required|in:cash,card',
            'type'                => 'required|in:clothes,sales,services',
            'wallet_used'         => 'required|bool',
        ];

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
   

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
