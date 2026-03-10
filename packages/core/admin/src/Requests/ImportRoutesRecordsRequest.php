<?php

namespace Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportRoutesRecordsRequest extends FormRequest
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
			 "data.*.end_point"   =>  ['nullable','string'], 
			 "data.*.user_id"     =>  ['nullable','exists:users,id'], 
			 "data.*.ip_address"  =>  ['nullable','exists:users,id'], 
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
			 "data.*.end_point.string"     =>  trans('end point should be a string'), 
			 "data.*.end_point.required"   =>  trans('end point is required'), 
			 "data.*.user_id.exists"       =>  trans('user is not Valid'), 
			 "data.*.user_id.required"     =>  trans('user is required'), 
			 "data.*.ip_address.exists"    =>  trans('ip_address is not Valid'), 
			 "data.*.ip_address.required"  =>  trans('ip_address is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
