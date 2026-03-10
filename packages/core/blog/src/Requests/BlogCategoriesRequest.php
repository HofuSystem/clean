<?php

namespace Core\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class BlogCategoriesRequest extends FormRequest
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
			 "translations.en.title"  =>  ['required','string'], 
			 "translations.ar.title"  =>  ['required','string'], 
			 "parent_id"              =>  ['nullable','exists:blog_categories,id'], 
			 "status"                 =>  ['required','in:active,not-active'], 
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
			 "translations.en.title.string"    =>  trans('title should be a string'), 
			 "translations.en.title.required"  =>  trans('title is required'), 
			 "translations.ar.title.string"    =>  trans('title should be a string'), 
			 "translations.ar.title.required"  =>  trans('title is required'), 
			 "parent_id.exists"                =>  trans('parent is not Valid'), 
			 "parent_id.required"              =>  trans('parent is required'), 
			 "status.in"                       =>  trans('status is not allowed'), 
			 "status.required"                 =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
