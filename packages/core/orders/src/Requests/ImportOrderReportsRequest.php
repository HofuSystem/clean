<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderReportsRequest extends FormRequest
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
			 "data.*.order_id"          =>  ['required','exists:orders,id'], 
			 "data.*.user_id"           =>  ['required','exists:users,id'], 
			 "data.*.report_reason_id"  =>  ['required','exists:report_reasons,id'], 
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
			 "data.*.order_id.exists"            =>  trans('order is not Valid'), 
			 "data.*.order_id.required"          =>  trans('order is required'), 
			 "data.*.user_id.exists"             =>  trans('user is not Valid'), 
			 "data.*.user_id.required"           =>  trans('user is required'), 
			 "data.*.report_reason_id.exists"    =>  trans('report reason is not Valid'), 
			 "data.*.report_reason_id.required"  =>  trans('report reason is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
