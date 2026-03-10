<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportBansRequest extends FormRequest
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
			 "data.*.level"     =>  ['required','in:ip,device,user'], 
			 "data.*.value"     =>  ['required','string'], 
			 "data.*.admin_id"  =>  ['nullable','exists:users,id'], 
			 "data.*.from"      =>  ['nullable','date'], 
			 "data.*.to"        =>  ['nullable','date'], 
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
			 "data.*.level.in"           =>  trans('level is not allowed'), 
			 "data.*.level.required"     =>  trans('level is required'), 
			 "data.*.value.string"       =>  trans('value should be a string'), 
			 "data.*.value.required"     =>  trans('value is required'), 
			 "data.*.admin_id.exists"    =>  trans('admin is not Valid'), 
			 "data.*.admin_id.required"  =>  trans('admin is required'), 
			 "data.*.from.date"          =>  trans('starts should be a date'), 
			 "data.*.from.required"      =>  trans('starts is required'), 
			 "data.*.to.date"            =>  trans('ends should be a date'), 
			 "data.*.to.required"        =>  trans('ends is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
