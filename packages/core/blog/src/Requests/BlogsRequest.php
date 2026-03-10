<?php

namespace Core\Blog\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class BlogsRequest extends FormRequest
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
			 "slug"                     =>  ['required','unique:blogs,slug,'.$this->id,'string'],
			 "translations.en.title"    =>  ['required','string'],
			 "translations.ar.title"    =>  ['required','string'],
			 "translations.en.content"  =>  ['nullable','string'],
			 "translations.ar.content"  =>  ['nullable','string'],
			 "status"                   =>  ['required','in:pending,publish'],
			 "published_at"             =>  ['nullable','date'],
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
			 "translations.en.title.string"      =>  trans('title should be a string'),
			 "translations.en.title.required"    =>  trans('title is required'),
			 "translations.ar.title.string"      =>  trans('title should be a string'),
			 "translations.ar.title.required"    =>  trans('title is required'),
			 "translations.en.content.string"    =>  trans('content should be a string'),
			 "translations.en.content.required"  =>  trans('content is required'),
			 "translations.ar.content.string"    =>  trans('content should be a string'),
			 "translations.ar.content.required"  =>  trans('content is required'),
			 "category_id.exists"                =>  trans('category is not Valid'),
			 "category_id.required"              =>  trans('category is required'),
			 "status.in"                         =>  trans('status is not allowed'),
			 "status.required"                   =>  trans('status is required'),
			 "published_at.date"                 =>  trans('published_at should be a date'),
			 "published_at.required"             =>  trans('published_at is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
