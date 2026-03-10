<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ReportReasonsRequest extends FormRequest
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
			 "ordering"              =>  ['nullable','numeric'], 
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
			 "ordering.numeric"               =>  trans('ordering should be a number'), 
			 "ordering.required"              =>  trans('ordering is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
