<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class MapPointsRequest extends FormRequest
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
			 "lat"          =>  ['required','string'], 
			 "lng"          =>  ['required','string'], 
			 "district_id"  =>  ['required','exists:districts,id'], 
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
			 "lat.string"            =>  trans('lat should be a string'), 
			 "lat.required"          =>  trans('lat is required'), 
			 "lng.string"            =>  trans('lng should be a string'), 
			 "lng.required"          =>  trans('lng is required'), 
			 "district_id.exists"    =>  trans('district is not Valid'), 
			 "district_id.required"  =>  trans('district is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
