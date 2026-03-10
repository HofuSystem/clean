<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderReportsRequest extends FormRequest
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
			 "order_id"          =>  ['required','exists:orders,id'], 
			 "user_id"           =>  ['required','exists:users,id'], 
			 "report_reason_id"  =>  ['required','exists:report_reasons,id'], 
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
			 "order_id.exists"            =>  trans('order is not Valid'), 
			 "order_id.required"          =>  trans('order is required'), 
			 "user_id.exists"             =>  trans('user is not Valid'), 
			 "user_id.required"           =>  trans('user is required'), 
			 "report_reason_id.exists"    =>  trans('report reason is not Valid'), 
			 "report_reason_id.required"  =>  trans('report reason is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
