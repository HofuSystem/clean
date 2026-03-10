<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportSlidersRequest extends FormRequest
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
			 "data.*.category_id"  =>  ['required','exists:categories,id'], 
			 "data.*.type"         =>  ['required','in:services,sales,clothes,host,maid'], 
			 "data.*.status"       =>  ['required','in:active,not-active'], 
			 "data.*.city_id"      =>  ['nullable','exists:cities,id'], 
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
			 "data.*.category_id.exists"    =>  trans('category is not Valid'), 
			 "data.*.category_id.required"  =>  trans('category is required'), 
			 "data.*.type.in"               =>  trans('type is not allowed'), 
			 "data.*.type.required"         =>  trans('type is required'), 
			 "data.*.status.in"             =>  trans('status is not allowed'), 
			 "data.*.status.required"       =>  trans('status is required'), 
			 "data.*.city_id.exists"        =>  trans('city is not Valid'), 
			 "data.*.city_id.required"      =>  trans('city is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
