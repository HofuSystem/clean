<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderTransactionsRequest extends FormRequest
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
			 "data.*.order_id"               =>  ['required','exists:orders,id'], 
			 "data.*.type"                   =>  ['required','string'], 
			 "data.*.online_payment_method"  =>  ['nullable','string'], 
			 "data.*.amount"                 =>  ['required','numeric'], 
			 "data.*.transaction_id"         =>  ['nullable','string'], 
			 "data.*.point_id"               =>  ['nullable','exists:coupons,id'], 
			 "data.*.wallet_transaction_id"  =>  ['nullable','exists:wallet_transactions,id'], 
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
			 "data.*.order_id.exists"                 =>  trans('order is not Valid'), 
			 "data.*.order_id.required"               =>  trans('order is required'), 
			 "data.*.type.string"                     =>  trans('type should be a string'), 
			 "data.*.type.required"                   =>  trans('type is required'), 
			 "data.*.online_payment_method.string"    =>  trans('online payment method should be a string'), 
			 "data.*.online_payment_method.required"  =>  trans('online payment method is required'), 
			 "data.*.amount.numeric"                  =>  trans('amount should be a number'), 
			 "data.*.amount.required"                 =>  trans('amount is required'), 
			 "data.*.transaction_id.string"           =>  trans('transaction id should be a string'), 
			 "data.*.transaction_id.required"         =>  trans('transaction id is required'), 
			 "data.*.point_id.exists"                 =>  trans('point is not Valid'), 
			 "data.*.point_id.required"               =>  trans('point is required'), 
			 "data.*.wallet_transaction_id.exists"    =>  trans('wallet Transactions is not Valid'), 
			 "data.*.wallet_transaction_id.required"  =>  trans('wallet Transactions is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
