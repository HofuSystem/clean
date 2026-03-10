<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportFavsRequest extends FormRequest
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
			 "data.*.favs_type"  =>  ['required','string'], 
			 "data.*.favs_id"    =>  ['required','string'], 
			 "data.*.user_id"    =>  ['required','exists:users,id'], 
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
			 "data.*.favs_type.string"    =>  trans('favs type should be a string'), 
			 "data.*.favs_type.required"  =>  trans('favs type is required'), 
			 "data.*.favs_id.string"      =>  trans('favs should be a string'), 
			 "data.*.favs_id.required"    =>  trans('favs is required'), 
			 "data.*.user_id.exists"      =>  trans('user is not Valid'), 
			 "data.*.user_id.required"    =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
