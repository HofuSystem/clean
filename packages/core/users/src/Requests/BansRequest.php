<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class BansRequest extends FormRequest
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
			 "level"     =>  ['required','in:ip,device,user'], 
			 "value"     =>  ['required','string'], 
			 "admin_id"  =>  ['nullable','exists:users,id'], 
			 "from"      =>  ['nullable','date'], 
			 "to"        =>  ['nullable','date'], 
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
			 "level.in"           =>  trans('level is not allowed'), 
			 "level.required"     =>  trans('level is required'), 
			 "value.string"       =>  trans('value should be a string'), 
			 "value.required"     =>  trans('value is required'), 
			 "admin_id.exists"    =>  trans('admin is not Valid'), 
			 "admin_id.required"  =>  trans('admin is required'), 
			 "from.date"          =>  trans('starts should be a date'), 
			 "from.required"      =>  trans('starts is required'), 
			 "to.date"            =>  trans('ends should be a date'), 
			 "to.required"        =>  trans('ends is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
