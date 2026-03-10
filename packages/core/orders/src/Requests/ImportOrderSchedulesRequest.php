<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderSchedulesRequest extends FormRequest
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
			 "data.*.client_id"         =>  ['nullable','exists:users,id'], 
			 "data.*.type"              =>  ['required','in:day,date'], 
			 "data.*.receiver_day"      =>  ['required','in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'], 
			 "data.*.receiver_date"     =>  ['required','date'], 
			 "data.*.receiver_time"     =>  ['required','time'], 
			 "data.*.receiver_to_time"  =>  ['required','time'], 
			 "data.*.delivery_day"      =>  ['required','in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'], 
			 "data.*.delivery_date"     =>  ['required','date'], 
			 "data.*.delivery_time"     =>  ['required','time'], 
			 "data.*.delivery_to_time"  =>  ['required','time'], 
			 "data.*.receiver_address_id" =>  ['required','exists:addresses,id'], 
			 "data.*.delivery_address_id" =>  ['required','exists:addresses,id'], 
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
			 "data.*.client_id.exists"           =>  trans('client is not Valid'), 
			 "data.*.client_id.required"         =>  trans('client is required'), 
			 "data.*.type.in"                    =>  trans('type is not allowed'), 
			 "data.*.type.required"              =>  trans('type is required'), 
			 "data.*.receiver_day.in"            =>  trans('receiver day is not allowed'), 
			 "data.*.receiver_day.required"      =>  trans('receiver day is required'), 
			 "data.*.receiver_date.date"         =>  trans('receiver date should be a date'), 
			 "data.*.receiver_date.required"     =>  trans('receiver date is required'), 
			 "data.*.receiver_time.time"         =>  trans('receiver time should be a time'), 
			 "data.*.receiver_time.required"     =>  trans('receiver time is required'), 
			 "data.*.receiver_to_time.time"      =>  trans('receiver to time should be a time'), 
			 "data.*.receiver_to_time.required"  =>  trans('receiver to time is required'), 
			 "data.*.delivery_day.in"            =>  trans('delivery day is not allowed'), 
			 "data.*.delivery_day.required"      =>  trans('delivery day is required'), 
			 "data.*.delivery_date.date"         =>  trans('delivery date should be a date'), 
			 "data.*.delivery_date.required"     =>  trans('delivery date is required'), 
			 "data.*.delivery_time.time"         =>  trans('delivery time should be a time'), 
			 "data.*.delivery_time.required"     =>  trans('delivery time is required'), 
			 "data.*.delivery_to_time.time"      =>  trans('delivery to time should be a time'), 
			 "data.*.delivery_to_time.required"  =>  trans('delivery to time is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
