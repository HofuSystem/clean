<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCartsRequest extends FormRequest
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
			 "data.*.user_id"  =>  ['required','unique:carts,user_id','exists:users,id'], 
			 "data.*.phone"    =>  ['nullable','string'], 
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
			 "data.*.user_id.exists"    =>  trans('user is not Valid'), 
			 "data.*.user_id.required"  =>  trans('user is required'), 
			 "data.*.user_id.unique"    =>  trans('user should be unique'), 
			 "data.*.phone.string"      =>  trans('phone should be a string'), 
			 "data.*.phone.required"    =>  trans('phone is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
