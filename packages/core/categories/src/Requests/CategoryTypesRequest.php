<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryTypesRequest extends FormRequest
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
			 
			 "translations.en.name"  =>  ['required','string'], 
			 "translations.ar.name"  =>  ['required','string'], 
			 "category_id"           =>  ['required','exists:categories,id'], 
			 "hour_price"            =>  ['required','numeric'], 
			 "status"                =>  ['required','in:active,not-active'], 
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
			 "slug.string"                    =>  trans('slug should be a string'), 
			 "slug.required"                  =>  trans('slug is required'), 
			 "slug.unique"                    =>  trans('slug should be unique'), 
			 "translations.en.name.string"    =>  trans('name should be a string'), 
			 "translations.en.name.required"  =>  trans('name is required'), 
			 "translations.ar.name.string"    =>  trans('name should be a string'), 
			 "translations.ar.name.required"  =>  trans('name is required'), 
			 "category_id.exists"             =>  trans('category is not Valid'), 
			 "category_id.required"           =>  trans('category is required'), 
			 "hour_price.numeric"             =>  trans('hour price should be a number'), 
			 "hour_price.required"            =>  trans('hour price is required'), 
			 "status.in"                      =>  trans('status is not allowed'), 
			 "status.required"                =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
