<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class NationalitiesRequest extends FormRequest
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
			 "translations.en.name"  =>  ['required','string'], 
			 "translations.ar.name"  =>  ['required','string'], 
			 "arranging"             =>  ['nullable','numeric'], 
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
			 "translations.en.name.string"    =>  trans('name should be a string'), 
			 "translations.en.name.required"  =>  trans('name is required'), 
			 "translations.ar.name.string"    =>  trans('name should be a string'), 
			 "translations.ar.name.required"  =>  trans('name is required'), 
			 "arranging.numeric"              =>  trans('arranging should be a number'), 
			 "arranging.required"             =>  trans('arranging is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
