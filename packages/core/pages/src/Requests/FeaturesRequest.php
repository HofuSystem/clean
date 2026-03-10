<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class FeaturesRequest extends FormRequest
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
		 "section"                =>  ['nullable','in:b2b,b2c,services'],
		 "is_active"              =>  ['nullable','boolean'],
		 "image"                  =>  ['nullable','string'],
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
		 "section.in"                      =>  trans('section must be b2b, b2c or services'),
		 "is_active.boolean"               =>  trans('is active should be a boolean'),
		 "is_active.required"              =>  trans('is active is required'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
