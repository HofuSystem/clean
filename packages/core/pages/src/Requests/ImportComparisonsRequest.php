<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportComparisonsRequest extends FormRequest
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
		 "data.*.translations.en.point"       =>  ['required','string'],
		 "data.*.translations.ar.point"       =>  ['required','string'],
		 "data.*.translations.en.us_text"     =>  ['required','string'],
		 "data.*.translations.ar.us_text"     =>  ['required','string'],
		 "data.*.translations.en.them_text"   =>  ['required','string'],
		 "data.*.translations.ar.them_text"   =>  ['required','string'],
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
		 "data.*.translations.en.point.string"       =>  trans('point should be a string'),
		 "data.*.translations.en.point.required"     =>  trans('point is required'),
		 "data.*.translations.ar.point.string"       =>  trans('point should be a string'),
		 "data.*.translations.ar.point.required"     =>  trans('point is required'),
		 "data.*.translations.en.us_text.string"     =>  trans('us text should be a string'),
		 "data.*.translations.en.us_text.required"   =>  trans('us text is required'),
		 "data.*.translations.ar.us_text.string"     =>  trans('us text should be a string'),
		 "data.*.translations.ar.us_text.required"   =>  trans('us text is required'),
		 "data.*.translations.en.them_text.string"   =>  trans('them text should be a string'),
		 "data.*.translations.en.them_text.required" =>  trans('them text is required'),
		 "data.*.translations.ar.them_text.string"   =>  trans('them text should be a string'),
		 "data.*.translations.ar.them_text.required" =>  trans('them text is required'),
		 "data.*.order.integer"                      =>  trans('order should be an integer'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

