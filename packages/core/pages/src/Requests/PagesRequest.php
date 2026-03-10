<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PagesRequest extends FormRequest
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
			 "slug"                                    =>  ['required','string'],
			 "translations.en.title"                   =>  ['required','string'],
			 "translations.ar.title"                   =>  ['required','string'],
			 "is_active"                               =>  ['nullable','boolean'],
			 "sections"                                =>  ['nullable','array'],
			 "sections.*.translations.en.title"        =>  ['required','string'],
			 "sections.*.translations.ar.title"        =>  ['required','string'],
			 "sections.*.translations.en.small_title"  =>  ['nullable','string'],
			 "sections.*.translations.ar.small_title"  =>  ['nullable','string'],
			 "sections.*.translations.en.description"  =>  ['nullable','string'],
			 "sections.*.translations.ar.description"  =>  ['nullable','string'],
			 "sections.*.video"                        =>  ['nullable','string'],
			 "sections.*.template"                     =>  ['nullable','in:'],
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
			 "slug.string"                                      =>  trans('slug should be a string'),
			 "slug.required"                                    =>  trans('slug is required'),
			 "translations.en.title.string"                     =>  trans('title should be a string'),
			 "translations.en.title.required"                   =>  trans('title is required'),
			 "translations.ar.title.string"                     =>  trans('title should be a string'),
			 "translations.ar.title.required"                   =>  trans('title is required'),
			 "is_active.boolean"                                =>  trans('is active should be a boolean'),
			 "is_active.required"                               =>  trans('is active is required'),
			 "sections.array"                                   =>  trans('sections is not array'),
			 "sections.*.translations.en.title.string"          =>  trans('title should be a string'),
			 "sections.*.translations.en.title.required"        =>  trans('title is required'),
			 "sections.*.translations.ar.title.string"          =>  trans('title should be a string'),
			 "sections.*.translations.ar.title.required"        =>  trans('title is required'),
			 "sections.*.translations.en.small_title.string"    =>  trans('small title should be a string'),
			 "sections.*.translations.en.small_title.required"  =>  trans('small title is required'),
			 "sections.*.translations.ar.small_title.string"    =>  trans('small title should be a string'),
			 "sections.*.translations.ar.small_title.required"  =>  trans('small title is required'),
			 "sections.*.translations.en.description.string"    =>  trans('description should be a string'),
			 "sections.*.translations.en.description.required"  =>  trans('description is required'),
			 "sections.*.translations.ar.description.string"    =>  trans('description should be a string'),
			 "sections.*.translations.ar.description.required"  =>  trans('description is required'),
			 "sections.*.video.string"                          =>  trans('video should be a string'),
			 "sections.*.video.required"                        =>  trans('video is required'),
			 "sections.*.template.in"                           =>  trans('template is not allowed'),
			 "sections.*.template.required"                     =>  trans('template is required'),
			 "sections.*.page_id.exists"                        =>  trans('page is not Valid'),
			 "sections.*.page_id.required"                      =>  trans('page is required'),
			 "sections.required"                                =>  trans('sections is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
