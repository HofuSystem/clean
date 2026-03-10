<?php

namespace Core\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportWalletPackagesRequest extends FormRequest
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
			 "data.*.price"   =>  ['required','numeric'], 
			 "data.*.value"   =>  ['required','numeric'], 
			 "data.*.status"  =>  ['required','in:active,not active'], 
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
			 "data.*.price.numeric"    =>  trans('price should be a number'), 
			 "data.*.price.required"   =>  trans('price is required'), 
			 "data.*.value.numeric"    =>  trans('value should be a number'), 
			 "data.*.value.required"   =>  trans('value is required'), 
			 "data.*.status.in"        =>  trans('status is not allowed'), 
			 "data.*.status.required"  =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
