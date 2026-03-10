<?php

namespace Core\Info\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class FavsRequest extends FormRequest
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
			 "favs_type"  =>  ['required','string'], 
			 "favs_id"    =>  ['required','string'], 
			 "user_id"    =>  ['required','exists:users,id'], 
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
			 "favs_type.string"    =>  trans('favs type should be a string'), 
			 "favs_type.required"  =>  trans('favs type is required'), 
			 "favs_id.string"      =>  trans('favs should be a string'), 
			 "favs_id.required"    =>  trans('favs is required'), 
			 "user_id.exists"      =>  trans('user is not Valid'), 
			 "user_id.required"    =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
