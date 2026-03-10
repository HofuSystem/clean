<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CartsRequest extends FormRequest
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
			 "user_id"  =>  ['required','unique:carts,user_id','exists:users,id'], 
			 "phone"    =>  ['nullable','string'], 
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
			 "user_id.exists"    =>  trans('user is not Valid'), 
			 "user_id.required"  =>  trans('user is required'), 
			 "user_id.unique"    =>  trans('user should be unique'), 
			 "phone.string"      =>  trans('phone should be a string'), 
			 "phone.required"    =>  trans('phone is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
