<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderInvoicesRequest extends FormRequest
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
			 "invoice_num"  =>  ['required','string'], 
			 "order_id"     =>  ['required','exists:orders,id'], 
			 "user_id"      =>  ['nullable','exists:users,id'], 
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
			 "invoice_num.string"    =>  trans('invoice num should be a string'), 
			 "invoice_num.required"  =>  trans('invoice num is required'), 
			 "order_id.exists"       =>  trans('order is not Valid'), 
			 "order_id.required"     =>  trans('order is required'), 
			 "user_id.exists"        =>  trans('user is not Valid'), 
			 "user_id.required"      =>  trans('user is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
