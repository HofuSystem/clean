<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class TestimonialsRequest extends FormRequest
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
			 "translations.en.name"      =>  ['required','string'],
			 "translations.ar.name"      =>  ['required','string'],
			 "translations.en.title"     =>  ['required','string'],
			 "translations.ar.title"     =>  ['required','string'],
			 "translations.en.location"  =>  ['required','string'],
			 "translations.ar.location"  =>  ['required','string'],
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
			 "translations.en.name.string"        =>  trans('name should be a string'),
			 "translations.en.name.required"      =>  trans('name is required'),
			 "translations.ar.name.string"        =>  trans('name should be a string'),
			 "translations.ar.name.required"      =>  trans('name is required'),
			 "translations.en.title.string"       =>  trans('title should be a string'),
			 "translations.en.title.required"     =>  trans('title is required'),
			 "translations.ar.title.string"       =>  trans('title should be a string'),
			 "translations.ar.title.required"     =>  trans('title is required'),
			 "translations.en.location.string"    =>  trans('location should be a string'),
			 "translations.en.location.required"  =>  trans('location is required'),
			 "translations.ar.location.string"    =>  trans('location should be a string'),
			 "translations.ar.location.required"  =>  trans('location is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
