<?php

namespace Core\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportBlogsRequest extends FormRequest
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
			 "data.*.translations.en.title"    =>  ['required','string'], 
			 "data.*.translations.ar.title"    =>  ['required','string'], 
			 "data.*.translations.en.content"  =>  ['nullable','string'], 
			 "data.*.translations.ar.content"  =>  ['nullable','string'], 
			 "data.*.category_id"              =>  ['required','exists:blog_categories,id'], 
			 "data.*.status"                   =>  ['required','in:pending,publish'], 
			 "data.*.published_at"             =>  ['nullable','date'], 
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
			 "data.*.translations.en.title.string"      =>  trans('title should be a string'), 
			 "data.*.translations.en.title.required"    =>  trans('title is required'), 
			 "data.*.translations.ar.title.string"      =>  trans('title should be a string'), 
			 "data.*.translations.ar.title.required"    =>  trans('title is required'), 
			 "data.*.translations.en.content.string"    =>  trans('content should be a string'), 
			 "data.*.translations.en.content.required"  =>  trans('content is required'), 
			 "data.*.translations.ar.content.string"    =>  trans('content should be a string'), 
			 "data.*.translations.ar.content.required"  =>  trans('content is required'), 
			 "data.*.category_id.exists"                =>  trans('category is not Valid'), 
			 "data.*.category_id.required"              =>  trans('category is required'), 
			 "data.*.status.in"                         =>  trans('status is not allowed'), 
			 "data.*.status.required"                   =>  trans('status is required'), 
			 "data.*.published_at.date"                 =>  trans('published_at should be a date'), 
			 "data.*.published_at.required"             =>  trans('published_at is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
