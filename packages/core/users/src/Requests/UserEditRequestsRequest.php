<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class UserEditRequestsRequest extends FormRequest
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
			 "fullname"  =>  ['required','string'], 
			 "email"     =>  ['nullable','email'], 
			 "phone"     =>  ['required','string'], 
			 "user_id"   =>  ['required','exists:users,id'], 
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
			 "fullname.string"    =>  trans('full name should be a string'), 
			 "fullname.required"  =>  trans('full name is required'), 
			 "email.email"        =>  trans('email should be a email'), 
			 "email.required"     =>  trans('email is required'), 
			 "phone.string"       =>  trans('phone should be a string'), 
			 "phone.required"     =>  trans('phone is required'), 
			 "user_id.exists"     =>  trans('user is not Valid'), 
			 "user_id.required"   =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
