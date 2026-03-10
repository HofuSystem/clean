<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderTransactionsRequest extends FormRequest
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
			 "order_id"               =>  ['required','exists:orders,id'], 
			 "type"                   =>  ['required','string'], 
			 "online_payment_method"  =>  ['nullable','string'], 
			 "amount"                 =>  ['required','numeric'], 
			 "transaction_id"         =>  ['nullable','string'], 
			 "point_id"               =>  ['nullable','exists:coupons,id'], 
			 "wallet_transaction_id"  =>  ['nullable','exists:wallet_transactions,id'], 
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
			 "order_id.exists"                 =>  trans('order is not Valid'), 
			 "order_id.required"               =>  trans('order is required'), 
			 "type.string"                     =>  trans('type should be a string'), 
			 "type.required"                   =>  trans('type is required'), 
			 "online_payment_method.string"    =>  trans('online payment method should be a string'), 
			 "online_payment_method.required"  =>  trans('online payment method is required'), 
			 "amount.numeric"                  =>  trans('amount should be a number'), 
			 "amount.required"                 =>  trans('amount is required'), 
			 "transaction_id.string"           =>  trans('transaction id should be a string'), 
			 "transaction_id.required"         =>  trans('transaction id is required'), 
			 "point_id.exists"                 =>  trans('point is not Valid'), 
			 "point_id.required"               =>  trans('point is required'), 
			 "wallet_transaction_id.exists"    =>  trans('wallet Transactions is not Valid'), 
			 "wallet_transaction_id.required"  =>  trans('wallet Transactions is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
