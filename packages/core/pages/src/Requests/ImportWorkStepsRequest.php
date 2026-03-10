<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportWorkStepsRequest extends FormRequest
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
		 "data.*.translations.en.title"       =>  ['required','string'],
		 "data.*.translations.ar.title"       =>  ['required','string'],
		 "data.*.translations.en.description" =>  ['nullable','string'],
		 "data.*.translations.ar.description" =>  ['nullable','string'],
		 "data.*.icon"                        =>  ['nullable','string'],
		 "data.*.order"                       =>  ['nullable','integer'],
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
		 "data.*.translations.en.title.string"       =>  trans('title should be a string'),
		 "data.*.translations.en.title.required"     =>  trans('title is required'),
		 "data.*.translations.ar.title.string"       =>  trans('title should be a string'),
		 "data.*.translations.ar.title.required"     =>  trans('title is required'),
		 "data.*.translations.en.description.string" =>  trans('description should be a string'),
		 "data.*.translations.ar.description.string" =>  trans('description should be a string'),
		 "data.*.icon.string"                        =>  trans('icon should be a string'),
		 "data.*.order.integer"                      =>  trans('order should be an integer'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

