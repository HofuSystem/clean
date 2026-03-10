<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ComparisonsRequest extends FormRequest
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
		 "translations.en.point"       =>  ['required','string'],
		 "translations.ar.point"       =>  ['required','string'],
		 "translations.en.us_text"     =>  ['required','string'],
		 "translations.ar.us_text"     =>  ['required','string'],
		 "translations.en.them_text"   =>  ['required','string'],
		 "translations.ar.them_text"   =>  ['required','string'],
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
		 "translations.en.point.string"       =>  trans('point should be a string'),
		 "translations.en.point.required"     =>  trans('point is required'),
		 "translations.ar.point.string"       =>  trans('point should be a string'),
		 "translations.ar.point.required"     =>  trans('point is required'),
		 "translations.en.us_text.string"     =>  trans('us text should be a string'),
		 "translations.en.us_text.required"   =>  trans('us text is required'),
		 "translations.ar.us_text.string"     =>  trans('us text should be a string'),
		 "translations.ar.us_text.required"   =>  trans('us text is required'),
		 "translations.en.them_text.string"   =>  trans('them text should be a string'),
		 "translations.en.them_text.required" =>  trans('them text is required'),
		 "translations.ar.them_text.string"   =>  trans('them text should be a string'),
		 "translations.ar.them_text.required" =>  trans('them text is required'),
		 "order.integer"                      =>  trans('order should be an integer'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

