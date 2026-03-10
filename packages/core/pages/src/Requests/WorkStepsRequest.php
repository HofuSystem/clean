<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class WorkStepsRequest extends FormRequest
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
		 "translations.en.title"       =>  ['required','string'],
		 "translations.ar.title"       =>  ['required','string'],
		 "translations.en.description" =>  ['nullable','string'],
		 "translations.ar.description" =>  ['nullable','string'],
		 "icon"                        =>  ['nullable','string'],
		 "order"                       =>  ['nullable','integer'],
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
		 "translations.en.title.string"       =>  trans('title should be a string'),
		 "translations.en.title.required"     =>  trans('title is required'),
		 "translations.ar.title.string"       =>  trans('title should be a string'),
		 "translations.ar.title.required"     =>  trans('title is required'),
		 "translations.en.description.string" =>  trans('description should be a string'),
		 "translations.ar.description.string" =>  trans('description should be a string'),
		 "icon.string"                        =>  trans('icon should be a string'),
		 "order.integer"                      =>  trans('order should be an integer'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

