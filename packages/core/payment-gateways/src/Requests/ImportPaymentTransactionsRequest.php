<?php

namespace Core\PaymentGateways\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPaymentTransactionsRequest extends FormRequest
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
			 "data.*.transaction_id"  =>  ['required','string'], 
			 "data.*.for"             =>  ['nullable','string'], 
			 "data.*.status"          =>  ['nullable','string'], 
			 "data.*.payment_method"  =>  ['nullable','string'], 
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
			 "data.*.transaction_id.string"    =>  trans('transaction id should be a string'), 
			 "data.*.transaction_id.required"  =>  trans('transaction id is required'), 
			 "data.*.for.string"               =>  trans('for should be a string'), 
			 "data.*.for.required"             =>  trans('for is required'), 
			 "data.*.status.string"            =>  trans('status should be a string'), 
			 "data.*.status.required"          =>  trans('status is required'), 
			 "data.*.payment_method.string"    =>  trans('payment method should be a string'), 
			 "data.*.payment_method.required"  =>  trans('payment method is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
