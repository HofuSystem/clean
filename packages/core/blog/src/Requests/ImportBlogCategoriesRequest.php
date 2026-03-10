<?php

namespace Core\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportBlogCategoriesRequest extends FormRequest
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
			 "data.*.translations.en.title"  =>  ['required','string'], 
			 "data.*.translations.ar.title"  =>  ['required','string'], 
			 "data.*.parent_id"              =>  ['nullable','exists:blog_categories,id'], 
			 "data.*.status"                 =>  ['required','in:active,not-active'], 
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
			 "data.*.translations.en.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.en.title.required"  =>  trans('title is required'), 
			 "data.*.translations.ar.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.ar.title.required"  =>  trans('title is required'), 
			 "data.*.parent_id.exists"                =>  trans('parent is not Valid'), 
			 "data.*.parent_id.required"              =>  trans('parent is required'), 
			 "data.*.status.in"                       =>  trans('status is not allowed'), 
			 "data.*.status.required"                 =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
