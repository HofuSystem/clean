<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ProfilesRequest extends FormRequest
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
			 "country_id"       =>  ['nullable','exists:countries,id'], 
			 "city_id"          =>  ['nullable','exists:cities,id'], 
			 "district_id"      =>  ['nullable','exists:districts,id'], 
			 "other_city_name"  =>  ['nullable','string'], 
			 "user_id"          =>  ['required','exists:users,id'], 
			 "lat"              =>  ['nullable','numeric'], 
			 "lng"              =>  ['nullable','string'], 
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
			 "country_id.exists"         =>  trans('country is not Valid'), 
			 "country_id.required"       =>  trans('country is required'), 
			 "city_id.exists"            =>  trans('city is not Valid'), 
			 "city_id.required"          =>  trans('city is required'), 
			 "district_id.exists"        =>  trans('district is not Valid'), 
			 "district_id.required"      =>  trans('district is required'), 
			 "other_city_name.string"    =>  trans('other city name should be a string'), 
			 "other_city_name.required"  =>  trans('other city name is required'), 
			 "user_id.exists"            =>  trans('user is not Valid'), 
			 "user_id.required"          =>  trans('user is required'), 
			 "lat.numeric"               =>  trans('lat should be a number'), 
			 "lat.required"              =>  trans('lat is required'), 
			 "lng.string"                =>  trans('lng should be a string'), 
			 "lng.required"              =>  trans('lng is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
