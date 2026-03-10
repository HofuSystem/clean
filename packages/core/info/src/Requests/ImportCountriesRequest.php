<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCountriesRequest extends FormRequest
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
			 "data.*.translations.en.name"                       =>  ['required','string'], 
			 "data.*.translations.ar.name"                       =>  ['required','string'], 
			 "data.*.phonecode"                                  =>  ['nullable','string'], 
			 "data.*.short_name"                                 =>  ['required','unique:countries,short_name','string'], 
			 "data.*.flag"                                       =>  ['required','string'], 
			 "data.*.cities"                                     =>  ['nullable','array'], 
			 "data.*.cities.*.translations.en.name"              =>  ['required','string'], 
			 "data.*.cities.*.translations.ar.name"              =>  ['required','string'], 
			 "data.*.cities.*.lat"                               =>  ['nullable','numeric'], 
			 "data.*.cities.*.lng"                               =>  ['nullable','numeric'], 
			 "data.*.cities.*.postal_code"                       =>  ['nullable','string'], 
			 "data.*.cities.*.delivery_price"                    =>  ['nullable','numeric'], 
			 "data.*.cities.*.status"                            =>  ['nullable','in:active,not-active'], 
			 "data.*.cities.*.districts"                         =>  ['nullable','array'], 
			 "data.*.cities.*.districts.*.translations.en.name"  =>  ['required','string'], 
			 "data.*.cities.*.districts.*.translations.ar.name"  =>  ['required','string'], 
			 "data.*.cities.*.districts.*.lat"                   =>  ['nullable','numeric'], 
			 "data.*.cities.*.districts.*.lng"                   =>  ['nullable','numeric'], 
			 "data.*.cities.*.districts.*.postal_code"           =>  ['nullable','string'], 
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
			 "data.*.slug.string"                                         =>  trans('slug should be a string'), 
			 "data.*.slug.required"                                       =>  trans('slug is required'), 
			 "data.*.slug.unique"                                         =>  trans('slug should be unique'), 
			 "data.*.translations.en.name.string"                         =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"                       =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"                         =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"                       =>  trans('name is required'), 
			 "data.*.phonecode.string"                                    =>  trans('phonecode should be a string'), 
			 "data.*.phonecode.required"                                  =>  trans('phonecode is required'), 
			 "data.*.short_name.string"                                   =>  trans('short name should be a string'), 
			 "data.*.short_name.required"                                 =>  trans('short name is required'), 
			 "data.*.short_name.unique"                                   =>  trans('short name should be unique'), 
			 "data.*.flag.string"                                         =>  trans('flag should be a string'), 
			 "data.*.flag.required"                                       =>  trans('flag is required'), 
			 "data.*.cities.array"                                        =>  trans('cities is not array'), 
			 "data.*.cities.*.slug.string"                                =>  trans('slug should be a string'), 
			 "data.*.cities.*.slug.required"                              =>  trans('slug is required'), 
			 "data.*.cities.*.slug.unique"                                =>  trans('slug should be unique'), 
			 "data.*.cities.*.translations.en.name.string"                =>  trans('name should be a string'), 
			 "data.*.cities.*.translations.en.name.required"              =>  trans('name is required'), 
			 "data.*.cities.*.translations.ar.name.string"                =>  trans('name should be a string'), 
			 "data.*.cities.*.translations.ar.name.required"              =>  trans('name is required'), 
			 "data.*.cities.*.lat.numeric"                                =>  trans('lat should be a number'), 
			 "data.*.cities.*.lat.required"                               =>  trans('lat is required'), 
			 "data.*.cities.*.lng.numeric"                                =>  trans('lng should be a number'), 
			 "data.*.cities.*.lng.required"                               =>  trans('lng is required'), 
			 "data.*.cities.*.postal_code.string"                         =>  trans('postal code should be a string'), 
			 "data.*.cities.*.postal_code.required"                       =>  trans('postal code is required'), 
			 "data.*.cities.*.delivery_price.numeric"                     =>  trans('delivery price should be a number'), 
			 "data.*.cities.*.delivery_price.required"                    =>  trans('delivery price is required'), 
			 "data.*.cities.*.status.in"                                  =>  trans('status is not allowed'), 
			 "data.*.cities.*.status.required"                            =>  trans('status is required'), 
			 "data.*.cities.*.country_id.exists"                          =>  trans('country is not Valid'), 
			 "data.*.cities.*.country_id.required"                        =>  trans('country is required'), 
			 "data.*.cities.*.districts.array"                            =>  trans('districts is not array'), 
			 "data.*.cities.*.districts.*.slug.string"                    =>  trans('slug should be a string'), 
			 "data.*.cities.*.districts.*.slug.required"                  =>  trans('slug is required'), 
			 "data.*.cities.*.districts.*.slug.unique"                    =>  trans('slug should be unique'), 
			 "data.*.cities.*.districts.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.cities.*.districts.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.cities.*.districts.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.cities.*.districts.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.cities.*.districts.*.lat.numeric"                    =>  trans('lat should be a number'), 
			 "data.*.cities.*.districts.*.lat.required"                   =>  trans('lat is required'), 
			 "data.*.cities.*.districts.*.lng.numeric"                    =>  trans('lng should be a number'), 
			 "data.*.cities.*.districts.*.lng.required"                   =>  trans('lng is required'), 
			 "data.*.cities.*.districts.*.postal_code.string"             =>  trans('postal code should be a string'), 
			 "data.*.cities.*.districts.*.postal_code.required"           =>  trans('postal code is required'), 
			 "data.*.cities.*.districts.*.city_id.exists"                 =>  trans('city is not Valid'), 
			 "data.*.cities.*.districts.*.city_id.required"               =>  trans('city is required'), 
			 "data.*.cities.*.districts.required"                         =>  trans('districts is required'), 
			 "data.*.cities.required"                                     =>  trans('cities is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
