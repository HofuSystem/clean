<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ChangeStatusRequest extends FormRequest
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
			 "status"               => ['required','in:pending,issue,ready_to_delivered,in_the_way,technical_accepted,service_started,delivered,order_has_been_delivered_to_admin,finished,canceled'], 
			 "admin_cancel_reason"  =>  ['required_if:status,canceled','string'], 
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
        "status.required" => trans("The status field is required."),
        "status.in"       => trans("The selected status is invalid."),
        
        "admin_cancel_reason.required_if" => trans("The cancel reason is required when the status is canceled."),
        "admin_cancel_reason.string"      => trans("The cancel reason must be a valid string.")
    ];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
