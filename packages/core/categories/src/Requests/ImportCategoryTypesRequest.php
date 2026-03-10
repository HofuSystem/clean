<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategoryTypesRequest extends FormRequest
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
			 "data.*.translations.en.name"  =>  ['required','string'], 
			 "data.*.translations.ar.name"  =>  ['required','string'], 
			 "data.*.category_id"           =>  ['required','exists:categories,id'], 
			 "data.*.hour_price"            =>  ['required','numeric'], 
			 "data.*.status"                =>  ['required','in:active,not-active'], 
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
			 "data.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.category_id.exists"             =>  trans('category is not Valid'), 
			 "data.*.category_id.required"           =>  trans('category is required'), 
			 "data.*.hour_price.numeric"             =>  trans('hour price should be a number'), 
			 "data.*.hour_price.required"            =>  trans('hour price is required'), 
			 "data.*.status.in"                      =>  trans('status is not allowed'), 
			 "data.*.status.required"                =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
