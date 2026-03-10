<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CitiesRequest extends FormRequest
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
			 "translations.en.name"              =>  ['required','string'], 
			 "translations.ar.name"              =>  ['required','string'], 
			 "lat"                               =>  ['nullable','numeric'], 
			 "lng"                               =>  ['nullable','numeric'], 
			 "postal_code"                       =>  ['nullable','string'], 
			 "delivery_price"                    =>  ['nullable','numeric'], 
			 "status"                            =>  ['nullable','in:active,not-active'], 
			 "country_id"                        =>  ['required','exists:countries,id'], 
			 "districts"                         =>  ['nullable','array'], 
			 "districts.*.translations.en.name"  =>  ['required','string'], 
			 "districts.*.translations.ar.name"  =>  ['required','string'], 
			 "districts.*.lat"                   =>  ['nullable','numeric'], 
			 "districts.*.lng"                   =>  ['nullable','numeric'], 
			 "districts.*.postal_code"           =>  ['nullable','string'], 
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
			 "slug.string"                                =>  trans('slug should be a string'), 
			 "slug.required"                              =>  trans('slug is required'), 
			 "slug.unique"                                =>  trans('slug should be unique'), 
			 "translations.en.name.string"                =>  trans('name should be a string'), 
			 "translations.en.name.required"              =>  trans('name is required'), 
			 "translations.ar.name.string"                =>  trans('name should be a string'), 
			 "translations.ar.name.required"              =>  trans('name is required'), 
			 "lat.numeric"                                =>  trans('lat should be a number'), 
			 "lat.required"                               =>  trans('lat is required'), 
			 "lng.numeric"                                =>  trans('lng should be a number'), 
			 "lng.required"                               =>  trans('lng is required'), 
			 "postal_code.string"                         =>  trans('postal code should be a string'), 
			 "postal_code.required"                       =>  trans('postal code is required'), 
			 "delivery_price.numeric"                     =>  trans('delivery price should be a number'), 
			 "delivery_price.required"                    =>  trans('delivery price is required'), 
			 "status.in"                                  =>  trans('status is not allowed'), 
			 "status.required"                            =>  trans('status is required'), 
			 "country_id.exists"                          =>  trans('country is not Valid'), 
			 "country_id.required"                        =>  trans('country is required'), 
			 "districts.array"                            =>  trans('districts is not array'), 
			 "districts.*.slug.string"                    =>  trans('slug should be a string'), 
			 "districts.*.slug.required"                  =>  trans('slug is required'), 
			 "districts.*.slug.unique"                    =>  trans('slug should be unique'), 
			 "districts.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "districts.*.translations.en.name.required"  =>  trans('name is required'), 
			 "districts.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "districts.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "districts.*.lat.numeric"                    =>  trans('lat should be a number'), 
			 "districts.*.lat.required"                   =>  trans('lat is required'), 
			 "districts.*.lng.numeric"                    =>  trans('lng should be a number'), 
			 "districts.*.lng.required"                   =>  trans('lng is required'), 
			 "districts.*.postal_code.string"             =>  trans('postal code should be a string'), 
			 "districts.*.postal_code.required"           =>  trans('postal code is required'), 
			 "districts.*.city_id.exists"                 =>  trans('city is not Valid'), 
			 "districts.*.city_id.required"               =>  trans('city is required'), 
			 "districts.required"                         =>  trans('districts is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
