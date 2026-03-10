<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPagesRequest extends FormRequest
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
			 "data.*.slug"                                    =>  ['required','string'],
			 "data.*.translations.en.title"                   =>  ['required','string'],
			 "data.*.translations.ar.title"                   =>  ['required','string'],
			 "data.*.is_active"                               =>  ['nullable','boolean'],
			 "data.*.sections"                                =>  ['nullable','array'],
			 "data.*.sections.*.translations.en.title"        =>  ['required','string'],
			 "data.*.sections.*.translations.ar.title"        =>  ['required','string'],
			 "data.*.sections.*.translations.en.small_title"  =>  ['nullable','string'],
			 "data.*.sections.*.translations.ar.small_title"  =>  ['nullable','string'],
			 "data.*.sections.*.translations.en.description"  =>  ['nullable','string'],
			 "data.*.sections.*.translations.ar.description"  =>  ['nullable','string'],
			 "data.*.sections.*.video"                        =>  ['nullable','string'],
			 "data.*.sections.*.template"                     =>  ['nullable','in:'],
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
			 "data.*.slug.string"                                      =>  trans('slug should be a string'),
			 "data.*.slug.required"                                    =>  trans('slug is required'),
			 "data.*.translations.en.title.string"                     =>  trans('title should be a string'),
			 "data.*.translations.en.title.required"                   =>  trans('title is required'),
			 "data.*.translations.ar.title.string"                     =>  trans('title should be a string'),
			 "data.*.translations.ar.title.required"                   =>  trans('title is required'),
			 "data.*.is_active.boolean"                                =>  trans('is active should be a boolean'),
			 "data.*.is_active.required"                               =>  trans('is active is required'),
			 "data.*.sections.array"                                   =>  trans('sections is not array'),
			 "data.*.sections.*.translations.en.title.string"          =>  trans('title should be a string'),
			 "data.*.sections.*.translations.en.title.required"        =>  trans('title is required'),
			 "data.*.sections.*.translations.ar.title.string"          =>  trans('title should be a string'),
			 "data.*.sections.*.translations.ar.title.required"        =>  trans('title is required'),
			 "data.*.sections.*.translations.en.small_title.string"    =>  trans('small title should be a string'),
			 "data.*.sections.*.translations.en.small_title.required"  =>  trans('small title is required'),
			 "data.*.sections.*.translations.ar.small_title.string"    =>  trans('small title should be a string'),
			 "data.*.sections.*.translations.ar.small_title.required"  =>  trans('small title is required'),
			 "data.*.sections.*.translations.en.description.string"    =>  trans('description should be a string'),
			 "data.*.sections.*.translations.en.description.required"  =>  trans('description is required'),
			 "data.*.sections.*.translations.ar.description.string"    =>  trans('description should be a string'),
			 "data.*.sections.*.translations.ar.description.required"  =>  trans('description is required'),
			 "data.*.sections.*.video.string"                          =>  trans('video should be a string'),
			 "data.*.sections.*.video.required"                        =>  trans('video is required'),
			 "data.*.sections.*.template.in"                           =>  trans('template is not allowed'),
			 "data.*.sections.*.template.required"                     =>  trans('template is required'),
			 "data.*.sections.*.page_id.exists"                        =>  trans('page is not Valid'),
			 "data.*.sections.*.page_id.required"                      =>  trans('page is required'),
			 "data.*.sections.required"                                =>  trans('sections is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
