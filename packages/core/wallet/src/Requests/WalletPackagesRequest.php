<?php

namespace Core\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class WalletPackagesRequest extends FormRequest
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
			 "price"   =>  ['required','numeric'], 
			 "value"   =>  ['required','numeric'], 
			 "status"  =>  ['required','in:active,not active'], 
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
			 "price.numeric"    =>  trans('price should be a number'), 
			 "price.required"   =>  trans('price is required'), 
			 "value.numeric"    =>  trans('value should be a number'), 
			 "value.required"   =>  trans('value is required'), 
			 "status.in"        =>  trans('status is not allowed'), 
			 "status.required"  =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
