<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class AddressesRequest extends FormRequest
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
			 "location"     =>  ['required','string'],
			 "lat"          =>  ['nullable','string'],
			 "lng"          =>  ['nullable','string'],
			 "city_id"      =>  ['required','exists:cities,id'],
			 "district_id"  =>  ['required','exists:districts,id'],
			 "user_id"      =>  ['nullable','exists:users,id'],
             'is_default'   =>  ['nullable']

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
			 "location.string"       =>  trans('location should be a string'),
			 "location.required"     =>  trans('location is required'),
			 "lat.string"            =>  trans('lat should be a string'),
			 "lat.required"          =>  trans('lat is required'),
			 "lng.string"            =>  trans('lng should be a string'),
			 "lng.required"          =>  trans('lng is required'),
			 "city_id.exists"        =>  trans('city is not Valid'),
			 "city_id.required"      =>  trans('city is required'),
			 "district_id.exists"    =>  trans('district is not Valid'),
			 "district_id.required"  =>  trans('district is required'),
			 "user_id.exists"        =>  trans('user is not Valid'),
			 "user_id.required"      =>  trans('user is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
