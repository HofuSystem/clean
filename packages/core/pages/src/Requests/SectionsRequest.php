<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class SectionsRequest extends FormRequest
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
		 "translations.en.title"        =>  ['required','string'],
		 "translations.ar.title"        =>  ['required','string'],
		 "translations.en.small_title"  =>  ['nullable','string'],
		 "translations.ar.small_title"  =>  ['nullable','string'],
		 "translations.en.description"  =>  ['nullable','string'],
		 "translations.ar.description"  =>  ['nullable','string'],
		 "video"                        =>  ['nullable','string'],
		 "template"                     =>  ['nullable','string'],
		 "page_id"                      =>  ['nullable','exists:pages,id'],
		 "order"                        =>  ['nullable','integer'],
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
		 "translations.en.title.string"          =>  trans('title should be a string'),
		 "translations.en.title.required"        =>  trans('title is required'),
		 "translations.ar.title.string"          =>  trans('title should be a string'),
		 "translations.ar.title.required"        =>  trans('title is required'),
		 "translations.en.small_title.string"    =>  trans('small title should be a string'),
		 "translations.en.small_title.required"  =>  trans('small title is required'),
		 "translations.ar.small_title.string"    =>  trans('small title should be a string'),
		 "translations.ar.small_title.required"  =>  trans('small title is required'),
		 "translations.en.description.string"    =>  trans('description should be a string'),
		 "translations.en.description.required"  =>  trans('description is required'),
		 "translations.ar.description.string"    =>  trans('description should be a string'),
		 "translations.ar.description.required"  =>  trans('description is required'),
		 "video.string"                          =>  trans('video should be a string'),
		 "video.required"                        =>  trans('video is required'),
		 "template.in"                           =>  trans('template is not allowed'),
		 "template.required"                     =>  trans('template is required'),
		 "page_id.exists"                        =>  trans('page is not Valid'),
		 "page_id.required"                      =>  trans('page is required'),
		 "order.integer"                         =>  trans('order should be an integer'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
