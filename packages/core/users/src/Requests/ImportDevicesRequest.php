<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportDevicesRequest extends FormRequest
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
			 "data.*.device_token"  =>  ['required','string'], 
			 "data.*.type"          =>  ['required','in:ios,android,huawei'], 
			 "data.*.user_id"       =>  ['nullable','exists:users,id'], 
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
			 "data.*.device_token.string"    =>  trans('device token should be a string'), 
			 "data.*.device_token.required"  =>  trans('device token is required'), 
			 "data.*.type.in"                =>  trans('type is not allowed'), 
			 "data.*.type.required"          =>  trans('type is required'), 
			 "data.*.user_id.exists"         =>  trans('user is not Valid'), 
			 "data.*.user_id.required"       =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
