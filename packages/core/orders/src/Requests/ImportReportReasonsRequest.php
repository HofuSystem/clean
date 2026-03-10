<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportReportReasonsRequest extends FormRequest
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
			 "data.*.ordering"              =>  ['nullable','numeric'], 
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
			 "data.*.ordering.numeric"               =>  trans('ordering should be a number'), 
			 "data.*.ordering.required"              =>  trans('ordering is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
