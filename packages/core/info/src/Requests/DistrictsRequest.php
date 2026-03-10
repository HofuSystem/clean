<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class DistrictsRequest extends FormRequest
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
			 "slug"                  =>  ['required'], 
			 "translations.en.name"  =>  ['required','string'], 
			 "translations.ar.name"  =>  ['required','string'], 
			 "postal_code"           =>  ['nullable','string'], 
			 "city_id"               =>  ['required','exists:cities,id'], 
			 "coordinates"           =>  ['nullable','array'],  
			 "coordinates.*.lat"     =>  ['nullable','numeric'],
			 "coordinates.*.lng"     =>  ['nullable','numeric'],
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
			 "slug.string"                       =>  trans('slug should be a string'), 
			 "slug.required"                     =>  trans('slug is required'), 
			 "slug.unique"                       =>  trans('slug should be unique'), 
			 "translations.en.name.string"       =>  trans('name should be a string'), 
			 "translations.en.name.required"     =>  trans('name is required'), 
			 "translations.ar.name.string"       =>  trans('name should be a string'), 
			 "translations.ar.name.required"     =>  trans('name is required'), 
			 "lat.numeric"                       =>  trans('lat should be a number'), 
			 "lat.required"                      =>  trans('lat is required'), 
			 "lng.numeric"                       =>  trans('lng should be a number'), 
			 "lng.required"                      =>  trans('lng is required'), 
			 "postal_code.string"                =>  trans('postal code should be a string'), 
			 "postal_code.required"              =>  trans('postal code is required'), 
			 "city_id.exists"                    =>  trans('city is not Valid'), 
			 "city_id.required"                  =>  trans('city is required'), 
			 "coordinates.array"                 =>  trans('coordinates should be an array'), 
			 "coordinates.*.lat.numeric"         =>  trans('lat should be a number'), 
			 "coordinates.*.lng.numeric"         =>  trans('lng should be a number'), 


			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
