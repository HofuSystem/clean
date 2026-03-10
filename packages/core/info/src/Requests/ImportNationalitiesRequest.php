<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportNationalitiesRequest extends FormRequest
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
			 "data.*.translations.en.name"  =>  ['required','string'], 
			 "data.*.translations.ar.name"  =>  ['required','string'], 
			 "data.*.arranging"             =>  ['nullable','numeric'], 
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
			 "data.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.arranging.numeric"              =>  trans('arranging should be a number'), 
			 "data.*.arranging.required"             =>  trans('arranging is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
