<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportDistrictsRequest extends FormRequest
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
			 "data.*.lat"                   =>  ['nullable','numeric'], 
			 "data.*.lng"                   =>  ['nullable','numeric'], 
			 "data.*.postal_code"           =>  ['nullable','string'], 
			 "data.*.city_id"               =>  ['required','exists:cities,id'], 
			 "data.*.slug"                  =>  ['nullable'], 

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
			 "data.*.slug.string"                       =>  trans('slug should be a string'), 
			 "data.*.slug.required"                     =>  trans('slug is required'), 
			 "data.*.slug.unique"                       =>  trans('slug should be unique'), 
			 "data.*.translations.en.name.string"       =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"     =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"       =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"     =>  trans('name is required'), 
			 "data.*.lat.numeric"                       =>  trans('lat should be a number'), 
			 "data.*.lat.required"                      =>  trans('lat is required'), 
			 "data.*.lng.numeric"                       =>  trans('lng should be a number'), 
			 "data.*.lng.required"                      =>  trans('lng is required'), 
			 "data.*.postal_code.string"                =>  trans('postal code should be a string'), 
			 "data.*.postal_code.required"              =>  trans('postal code is required'), 
			 "data.*.city_id.exists"                    =>  trans('city is not Valid'), 
			 "data.*.city_id.required"                  =>  trans('city is required'), 
			
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
