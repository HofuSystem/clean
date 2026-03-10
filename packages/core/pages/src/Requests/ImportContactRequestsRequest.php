<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportContactRequestsRequest extends FormRequest
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
			 "data.*.name"        =>  ['required','string'], 
			 "data.*.phone"       =>  ['required','string'], 
			 "data.*.email"       =>  ['nullable','email'], 
			 "data.*.service_id"  =>  ['nullable','exists:testimonials,id'], 
			 "data.*.date"        =>  ['nullable','date'], 
			 "data.*.time"        =>  ['nullable','time'], 
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
			 "data.*.name.string"          =>  trans('name should be a string'), 
			 "data.*.name.required"        =>  trans('name is required'), 
			 "data.*.phone.string"         =>  trans('phone should be a string'), 
			 "data.*.phone.required"       =>  trans('phone is required'), 
			 "data.*.email.email"          =>  trans('email should be a email'), 
			 "data.*.email.required"       =>  trans('email is required'), 
			 "data.*.service_id.exists"    =>  trans('service is not Valid'), 
			 "data.*.service_id.required"  =>  trans('service is required'), 
			 "data.*.date.date"            =>  trans('date should be a date'), 
			 "data.*.date.required"        =>  trans('date is required'), 
			 "data.*.time.time"            =>  trans('time should be a time'), 
			 "data.*.time.required"        =>  trans('time is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
