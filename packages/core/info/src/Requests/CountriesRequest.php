<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CountriesRequest extends FormRequest
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
			 "translations.en.name"                       =>  ['required','string'], 
			 "translations.ar.name"                       =>  ['required','string'], 
			 "phonecode"                                  =>  ['nullable','string'], 
			 "short_name"                                 =>  ['required','unique:countries,short_name','string'], 
			 "flag"                                       =>  ['required','string'], 
			 "cities"                                     =>  ['nullable','array'], 
			 "cities.*.translations.en.name"              =>  ['required','string'], 
			 "cities.*.translations.ar.name"              =>  ['required','string'], 
			 "cities.*.lat"                               =>  ['nullable','numeric'], 
			 "cities.*.lng"                               =>  ['nullable','numeric'], 
			 "cities.*.postal_code"                       =>  ['nullable','string'], 
			 "cities.*.delivery_price"                    =>  ['nullable','numeric'], 
			 "cities.*.status"                            =>  ['nullable','in:active,not-active'], 
			 "cities.*.districts"                         =>  ['nullable','array'], 
			 "cities.*.districts.*.translations.en.name"  =>  ['required','string'], 
			 "cities.*.districts.*.translations.ar.name"  =>  ['required','string'], 
			 "cities.*.districts.*.lat"                   =>  ['nullable','numeric'], 
			 "cities.*.districts.*.lng"                   =>  ['nullable','numeric'], 
			 "cities.*.districts.*.postal_code"           =>  ['nullable','string'], 
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
			 "slug.string"                                         =>  trans('slug should be a string'), 
			 "slug.required"                                       =>  trans('slug is required'), 
			 "slug.unique"                                         =>  trans('slug should be unique'), 
			 "translations.en.name.string"                         =>  trans('name should be a string'), 
			 "translations.en.name.required"                       =>  trans('name is required'), 
			 "translations.ar.name.string"                         =>  trans('name should be a string'), 
			 "translations.ar.name.required"                       =>  trans('name is required'), 
			 "phonecode.string"                                    =>  trans('phonecode should be a string'), 
			 "phonecode.required"                                  =>  trans('phonecode is required'), 
			 "short_name.string"                                   =>  trans('short name should be a string'), 
			 "short_name.required"                                 =>  trans('short name is required'), 
			 "short_name.unique"                                   =>  trans('short name should be unique'), 
			 "flag.string"                                         =>  trans('flag should be a string'), 
			 "flag.required"                                       =>  trans('flag is required'), 
			 "cities.array"                                        =>  trans('cities is not array'), 
			 "cities.*.slug.string"                                =>  trans('slug should be a string'), 
			 "cities.*.slug.required"                              =>  trans('slug is required'), 
			 "cities.*.slug.unique"                                =>  trans('slug should be unique'), 
			 "cities.*.translations.en.name.string"                =>  trans('name should be a string'), 
			 "cities.*.translations.en.name.required"              =>  trans('name is required'), 
			 "cities.*.translations.ar.name.string"                =>  trans('name should be a string'), 
			 "cities.*.translations.ar.name.required"              =>  trans('name is required'), 
			 "cities.*.lat.numeric"                                =>  trans('lat should be a number'), 
			 "cities.*.lat.required"                               =>  trans('lat is required'), 
			 "cities.*.lng.numeric"                                =>  trans('lng should be a number'), 
			 "cities.*.lng.required"                               =>  trans('lng is required'), 
			 "cities.*.postal_code.string"                         =>  trans('postal code should be a string'), 
			 "cities.*.postal_code.required"                       =>  trans('postal code is required'), 
			 "cities.*.delivery_price.numeric"                     =>  trans('delivery price should be a number'), 
			 "cities.*.delivery_price.required"                    =>  trans('delivery price is required'), 
			 "cities.*.status.in"                                  =>  trans('status is not allowed'), 
			 "cities.*.status.required"                            =>  trans('status is required'), 
			 "cities.*.country_id.exists"                          =>  trans('country is not Valid'), 
			 "cities.*.country_id.required"                        =>  trans('country is required'), 
			 "cities.*.districts.array"                            =>  trans('districts is not array'), 
			 "cities.*.districts.*.slug.string"                    =>  trans('slug should be a string'), 
			 "cities.*.districts.*.slug.required"                  =>  trans('slug is required'), 
			 "cities.*.districts.*.slug.unique"                    =>  trans('slug should be unique'), 
			 "cities.*.districts.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "cities.*.districts.*.translations.en.name.required"  =>  trans('name is required'), 
			 "cities.*.districts.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "cities.*.districts.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "cities.*.districts.*.lat.numeric"                    =>  trans('lat should be a number'), 
			 "cities.*.districts.*.lat.required"                   =>  trans('lat is required'), 
			 "cities.*.districts.*.lng.numeric"                    =>  trans('lng should be a number'), 
			 "cities.*.districts.*.lng.required"                   =>  trans('lng is required'), 
			 "cities.*.districts.*.postal_code.string"             =>  trans('postal code should be a string'), 
			 "cities.*.districts.*.postal_code.required"           =>  trans('postal code is required'), 
			 "cities.*.districts.*.city_id.exists"                 =>  trans('city is not Valid'), 
			 "cities.*.districts.*.city_id.required"               =>  trans('city is required'), 
			 "cities.*.districts.required"                         =>  trans('districts is required'), 
			 "cities.required"                                     =>  trans('cities is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
