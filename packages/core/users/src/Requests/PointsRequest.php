<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PointsRequest extends FormRequest
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
			 "title"      =>  ['required','string'], 
			 "amount"     =>  ['required','string'], 
			 "operation"  =>  ['required','in:withdraw,deposit'], 
			 "expire_at"  =>  ['nullable','date'], 
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
			 "title.string"        =>  trans('title should be a string'), 
			 "title.required"      =>  trans('title is required'), 
			 "amount.string"       =>  trans('amount should be a string'), 
			 "amount.required"     =>  trans('amount is required'), 
			 "operation.in"        =>  trans('operation is not allowed'), 
			 "operation.required"  =>  trans('operation is required'), 
			 "expire_at.date"      =>  trans('expire at should be a date'), 
			 "expire_at.required"  =>  trans('expire at is required'), 
			 "user_id.exists"      =>  trans('user is not Valid'), 
			 "user_id.required"    =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
