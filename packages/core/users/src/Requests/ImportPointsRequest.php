<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPointsRequest extends FormRequest
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
			 "data.*.title"      =>  ['required','string'], 
			 "data.*.amount"     =>  ['required','string'], 
			 "data.*.operation"  =>  ['required','in:withdraw,deposit'], 
			 "data.*.expire_at"  =>  ['nullable','date'], 
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
			 "data.*.title.string"        =>  trans('title should be a string'), 
			 "data.*.title.required"      =>  trans('title is required'), 
			 "data.*.amount.string"       =>  trans('amount should be a string'), 
			 "data.*.amount.required"     =>  trans('amount is required'), 
			 "data.*.operation.in"        =>  trans('operation is not allowed'), 
			 "data.*.operation.required"  =>  trans('operation is required'), 
			 "data.*.expire_at.date"      =>  trans('expire at should be a date'), 
			 "data.*.expire_at.required"  =>  trans('expire at is required'), 
			 "data.*.user_id.exists"      =>  trans('user is not Valid'), 
			 "data.*.user_id.required"    =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
