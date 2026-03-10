<?php

namespace Core\PaymentGateways\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PaymentTransactionsRequest extends FormRequest
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
			 "transaction_id"  =>  ['required','string'], 
			 "for"             =>  ['nullable','string'], 
			 "status"          =>  ['nullable','string'], 
			 "payment_method"  =>  ['nullable','string'], 
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
			 "transaction_id.string"    =>  trans('transaction id should be a string'), 
			 "transaction_id.required"  =>  trans('transaction id is required'), 
			 "for.string"               =>  trans('for should be a string'), 
			 "for.required"             =>  trans('for is required'), 
			 "status.string"            =>  trans('status should be a string'), 
			 "status.required"          =>  trans('status is required'), 
			 "payment_method.string"    =>  trans('payment method should be a string'), 
			 "payment_method.required"  =>  trans('payment method is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
