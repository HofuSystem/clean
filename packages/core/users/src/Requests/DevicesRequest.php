<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class DevicesRequest extends FormRequest
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
			 "device_token"  =>  ['required','string'], 
			 "type"          =>  ['required','in:ios,android,huawei'], 
			 "user_id"       =>  ['nullable','exists:users,id'], 
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
			 "device_token.string"    =>  trans('device token should be a string'), 
			 "device_token.required"  =>  trans('device token is required'), 
			 "type.in"                =>  trans('type is not allowed'), 
			 "type.required"          =>  trans('type is required'), 
			 "user_id.exists"         =>  trans('user is not Valid'), 
			 "user_id.required"       =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
