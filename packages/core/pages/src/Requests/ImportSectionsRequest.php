<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportSectionsRequest extends FormRequest
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
			 "data.*.translations.en.title"        =>  ['required','string'],
			 "data.*.translations.ar.title"        =>  ['required','string'],
			 "data.*.translations.en.small_title"  =>  ['nullable','string'],
			 "data.*.translations.ar.small_title"  =>  ['nullable','string'],
			 "data.*.translations.en.description"  =>  ['nullable','string'],
			 "data.*.translations.ar.description"  =>  ['nullable','string'],
			 "data.*.video"                        =>  ['nullable','string'],
			 "data.*.template"                     =>  ['nullable','in:'],
			 "data.*.page_id"                      =>  ['nullable','exists:pages,id'],
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
			 "data.*.translations.en.title.string"          =>  trans('title should be a string'),
			 "data.*.translations.en.title.required"        =>  trans('title is required'),
			 "data.*.translations.ar.title.string"          =>  trans('title should be a string'),
			 "data.*.translations.ar.title.required"        =>  trans('title is required'),
			 "data.*.translations.en.small_title.string"    =>  trans('small title should be a string'),
			 "data.*.translations.en.small_title.required"  =>  trans('small title is required'),
			 "data.*.translations.ar.small_title.string"    =>  trans('small title should be a string'),
			 "data.*.translations.ar.small_title.required"  =>  trans('small title is required'),
			 "data.*.translations.en.description.string"    =>  trans('description should be a string'),
			 "data.*.translations.en.description.required"  =>  trans('description is required'),
			 "data.*.translations.ar.description.string"    =>  trans('description should be a string'),
			 "data.*.translations.ar.description.required"  =>  trans('description is required'),
			 "data.*.video.string"                          =>  trans('video should be a string'),
			 "data.*.video.required"                        =>  trans('video is required'),
			 "data.*.template.in"                           =>  trans('template is not allowed'),
			 "data.*.template.required"                     =>  trans('template is required'),
			 "data.*.page_id.exists"                        =>  trans('page is not Valid'),
			 "data.*.page_id.required"                      =>  trans('page is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
