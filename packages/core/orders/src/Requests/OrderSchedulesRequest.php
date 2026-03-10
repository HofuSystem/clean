<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderSchedulesRequest extends FormRequest
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
			  'client_id' => 'nullable|exists:users,id',
        'type' => 'required|in:day,date',
        'receiver_day' => 'required_if:type,day',
        'receiver_date' => 'required_if:type,date|date',
        'receiver_time' => 'required|date_format:H:i',
        'receiver_to_time' => 'required|date_format:H:i|after:receiver_time',
        'delivery_day' => 'required_if:type,day',
        'delivery_date' => 'required_if:type,date|date|after_or_equal:receiver_date',
        'delivery_time' => 'required|date_format:H:i',
        'delivery_to_time' => 'required|date_format:H:i|after:delivery_time',
        'receiver_address_id' => 'required|exists:addresses,id',
        'delivery_address_id' => 'required|exists:addresses,id',
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
			 "client_id.exists"           =>  trans('client is not Valid'), 
			 "client_id.required"         =>  trans('client is required'), 
			 "type.in"                    =>  trans('type is not allowed'), 
			 "type.required"              =>  trans('type is required'), 
			 "receiver_day.in"            =>  trans('receiver day is not allowed'), 
			 "receiver_day.required"      =>  trans('receiver day is required'), 
			 "receiver_date.date"         =>  trans('receiver date should be a date'), 
			 "receiver_date.required"     =>  trans('receiver date is required'), 
			 "receiver_time.time"         =>  trans('receiver time should be a time'), 
			 "receiver_time.required"     =>  trans('receiver time is required'), 
			 "receiver_to_time.time"      =>  trans('receiver to time should be a time'), 
			 "receiver_to_time.required"  =>  trans('receiver to time is required'), 
			 "delivery_day.in"            =>  trans('delivery day is not allowed'), 
			 "delivery_day.required"      =>  trans('delivery day is required'), 
			 "delivery_date.date"         =>  trans('delivery date should be a date'), 
			 "delivery_date.required"     =>  trans('delivery date is required'), 
			 "delivery_time.time"         =>  trans('delivery time should be a time'), 
			 "delivery_time.required"     =>  trans('delivery time is required'), 
			 "delivery_to_time.time"      =>  trans('delivery to time should be a time'), 
			 "delivery_to_time.required"  =>  trans('delivery to time is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
