<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportAddressesRequest extends FormRequest
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
			 "data.*.location"     =>  ['required','string'],
			 "data.*.lat"          =>  ['nullable','string'],
			 "data.*.lng"          =>  ['nullable','string'],
			 "data.*.city_id"      =>  ['required','exists:cities,id'],
			 "data.*.district_id"  =>  ['required','exists:districts,id'],
             "data.*.is_default"   =>   ['nullable'],
			 "data.*.user_id"      =>  ['nullable','exists:users,id'],
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
			 "data.*.location.string"       =>  trans('location should be a string'),
			 "data.*.location.required"     =>  trans('location is required'),
			 "data.*.lat.string"            =>  trans('lat should be a string'),
			 "data.*.lat.required"          =>  trans('lat is required'),
			 "data.*.lng.string"            =>  trans('lng should be a string'),
			 "data.*.lng.required"          =>  trans('lng is required'),
			 "data.*.city_id.exists"        =>  trans('city is not Valid'),
			 "data.*.city_id.required"      =>  trans('city is required'),
			 "data.*.district_id.exists"    =>  trans('district is not Valid'),
			 "data.*.district_id.required"  =>  trans('district is required'),
			 "data.*.user_id.exists"        =>  trans('user is not Valid'),
			 "data.*.user_id.required"      =>  trans('user is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
