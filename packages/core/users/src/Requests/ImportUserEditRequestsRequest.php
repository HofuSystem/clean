<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportUserEditRequestsRequest extends FormRequest
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
			 "data.*.fullname"  =>  ['required','string'], 
			 "data.*.email"     =>  ['nullable','email'], 
			 "data.*.phone"     =>  ['required','string'], 
			 "data.*.user_id"   =>  ['required','exists:users,id'], 
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
			 "data.*.fullname.string"    =>  trans('full name should be a string'), 
			 "data.*.fullname.required"  =>  trans('full name is required'), 
			 "data.*.email.email"        =>  trans('email should be a email'), 
			 "data.*.email.required"     =>  trans('email is required'), 
			 "data.*.phone.string"       =>  trans('phone should be a string'), 
			 "data.*.phone.required"     =>  trans('phone is required'), 
			 "data.*.user_id.exists"     =>  trans('user is not Valid'), 
			 "data.*.user_id.required"   =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
