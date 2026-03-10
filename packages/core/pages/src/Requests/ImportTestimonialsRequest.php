<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportTestimonialsRequest extends FormRequest
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
			 "data.*.translations.en.name"      =>  ['required','string'],
			 "data.*.translations.ar.name"      =>  ['required','string'],
			 "data.*.translations.en.title"     =>  ['required','string'],
			 "data.*.translations.ar.title"     =>  ['required','string'],
			 "data.*.translations.en.location"  =>  ['required','string'],
			 "data.*.translations.ar.location"  =>  ['required','string'],
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
			 "data.*.translations.en.name.string"        =>  trans('name should be a string'),
			 "data.*.translations.en.name.required"      =>  trans('name is required'),
			 "data.*.translations.ar.name.string"        =>  trans('name should be a string'),
			 "data.*.translations.ar.name.required"      =>  trans('name is required'),
			 "data.*.translations.en.title.string"       =>  trans('title should be a string'),
			 "data.*.translations.en.title.required"     =>  trans('title is required'),
			 "data.*.translations.ar.title.string"       =>  trans('title should be a string'),
			 "data.*.translations.ar.title.required"     =>  trans('title is required'),
			 "data.*.translations.en.location.string"    =>  trans('location should be a string'),
			 "data.*.translations.en.location.required"  =>  trans('location is required'),
			 "data.*.translations.ar.location.string"    =>  trans('location should be a string'),
			 "data.*.translations.ar.location.required"  =>  trans('location is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
