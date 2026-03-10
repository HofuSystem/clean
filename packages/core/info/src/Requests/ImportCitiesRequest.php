<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCitiesRequest extends FormRequest
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
			 "data.*.slug"                              =>  ['required','unique:cities,slug','string'], 
			 "data.*.translations.en.name"              =>  ['required','string'], 
			 "data.*.translations.ar.name"              =>  ['required','string'], 
			 "data.*.lat"                               =>  ['nullable','numeric'], 
			 "data.*.lng"                               =>  ['nullable','numeric'], 
			 "data.*.postal_code"                       =>  ['nullable','string'], 
			 "data.*.delivery_price"                    =>  ['nullable','numeric'], 
			 "data.*.status"                            =>  ['nullable','in:active,not-active'], 
			 "data.*.country_id"                        =>  ['required','exists:countries,id'], 
			 "data.*.districts"                         =>  ['nullable','array'], 
			 "data.*.districts.*.slug"                  =>  ['required','unique:districts,slug','string'], 
			 "data.*.districts.*.translations.en.name"  =>  ['required','string'], 
			 "data.*.districts.*.translations.ar.name"  =>  ['required','string'], 
			 "data.*.districts.*.lat"                   =>  ['nullable','numeric'], 
			 "data.*.districts.*.lng"                   =>  ['nullable','numeric'], 
			 "data.*.districts.*.postal_code"           =>  ['nullable','string'], 
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
			 "data.*.slug.string"                                =>  trans('slug should be a string'), 
			 "data.*.slug.required"                              =>  trans('slug is required'), 
			 "data.*.slug.unique"                                =>  trans('slug should be unique'), 
			 "data.*.translations.en.name.string"                =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"              =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"                =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"              =>  trans('name is required'), 
			 "data.*.lat.numeric"                                =>  trans('lat should be a number'), 
			 "data.*.lat.required"                               =>  trans('lat is required'), 
			 "data.*.lng.numeric"                                =>  trans('lng should be a number'), 
			 "data.*.lng.required"                               =>  trans('lng is required'), 
			 "data.*.postal_code.string"                         =>  trans('postal code should be a string'), 
			 "data.*.postal_code.required"                       =>  trans('postal code is required'), 
			 "data.*.delivery_price.numeric"                     =>  trans('delivery price should be a number'), 
			 "data.*.delivery_price.required"                    =>  trans('delivery price is required'), 
			 "data.*.status.in"                                  =>  trans('status is not allowed'), 
			 "data.*.status.required"                            =>  trans('status is required'), 
			 "data.*.country_id.exists"                          =>  trans('country is not Valid'), 
			 "data.*.country_id.required"                        =>  trans('country is required'), 
			 "data.*.districts.array"                            =>  trans('districts is not array'), 
			 "data.*.districts.*.slug.string"                    =>  trans('slug should be a string'), 
			 "data.*.districts.*.slug.required"                  =>  trans('slug is required'), 
			 "data.*.districts.*.slug.unique"                    =>  trans('slug should be unique'), 
			 "data.*.districts.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.districts.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.districts.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.districts.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.districts.*.lat.numeric"                    =>  trans('lat should be a number'), 
			 "data.*.districts.*.lat.required"                   =>  trans('lat is required'), 
			 "data.*.districts.*.lng.numeric"                    =>  trans('lng should be a number'), 
			 "data.*.districts.*.lng.required"                   =>  trans('lng is required'), 
			 "data.*.districts.*.postal_code.string"             =>  trans('postal code should be a string'), 
			 "data.*.districts.*.postal_code.required"           =>  trans('postal code is required'), 
			 "data.*.districts.*.city_id.exists"                 =>  trans('city is not Valid'), 
			 "data.*.districts.*.city_id.required"               =>  trans('city is required'), 
			 "data.*.districts.required"                         =>  trans('districts is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
