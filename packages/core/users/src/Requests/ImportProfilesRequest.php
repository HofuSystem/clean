<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportProfilesRequest extends FormRequest
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
			 "data.*.country_id"       =>  ['nullable','exists:countries,id'], 
			 "data.*.city_id"          =>  ['nullable','exists:cities,id'], 
			 "data.*.district_id"      =>  ['nullable','exists:districts,id'], 
			 "data.*.other_city_name"  =>  ['nullable','string'], 
			 "data.*.user_id"          =>  ['required','exists:users,id'], 
			 "data.*.lat"              =>  ['nullable','numeric'], 
			 "data.*.lng"              =>  ['nullable','string'], 
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
			 "data.*.country_id.exists"         =>  trans('country is not Valid'), 
			 "data.*.country_id.required"       =>  trans('country is required'), 
			 "data.*.city_id.exists"            =>  trans('city is not Valid'), 
			 "data.*.city_id.required"          =>  trans('city is required'), 
			 "data.*.district_id.exists"        =>  trans('district is not Valid'), 
			 "data.*.district_id.required"      =>  trans('district is required'), 
			 "data.*.other_city_name.string"    =>  trans('other city name should be a string'), 
			 "data.*.other_city_name.required"  =>  trans('other city name is required'), 
			 "data.*.user_id.exists"            =>  trans('user is not Valid'), 
			 "data.*.user_id.required"          =>  trans('user is required'), 
			 "data.*.lat.numeric"               =>  trans('lat should be a number'), 
			 "data.*.lat.required"              =>  trans('lat is required'), 
			 "data.*.lng.string"                =>  trans('lng should be a string'), 
			 "data.*.lng.required"              =>  trans('lng is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
