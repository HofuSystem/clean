<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderRepresentativesRequest extends FormRequest
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
			 "order_id"           =>  ['required','exists:orders,id'], 
			 "representative_id"  =>  ['nullable','exists:users,id'], 
			 "type"               =>  ['nullable','in:delivery,technical,receiver'], 
			 "date"               =>  ['nullable','date'], 
			 "time"               =>  ['nullable','time'], 
			 "to_time"            =>  ['nullable','time'], 
			 "lat"                =>  ['nullable','string'], 
			 "lng"                =>  ['nullable','string'], 
			 "location"           =>  ['nullable','string'], 
			 "has_problem"        =>  ['nullable','boolean'], 
			 "for_all_items"      =>  ['nullable','boolean'], 
			 "items"              =>  ['nullable','array'], 
			 "items.*"            =>  ['exists:order_items,id'], 
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
			 "order_id.exists"             =>  trans('order is not Valid'), 
			 "order_id.required"           =>  trans('order is required'), 
			 "representative_id.exists"    =>  trans('representative is not Valid'), 
			 "representative_id.required"  =>  trans('representative is required'), 
			 "type.in"                     =>  trans('type is not allowed'), 
			 "type.required"               =>  trans('type is required'), 
			 "date.date"                   =>  trans('date should be a date'), 
			 "date.required"               =>  trans('date is required'), 
			 "time.time"                   =>  trans('time should be a time'), 
			 "time.required"               =>  trans('time is required'), 
			 "to_time.time"                =>  trans('to time should be a time'), 
			 "to_time.required"            =>  trans('to time is required'), 
			 "lat.string"                  =>  trans('lat should be a string'), 
			 "lat.required"                =>  trans('lat is required'), 
			 "lng.string"                  =>  trans('lng should be a string'), 
			 "lng.required"                =>  trans('lng is required'), 
			 "location.string"             =>  trans('location should be a string'), 
			 "location.required"           =>  trans('location is required'), 
			 "has_problem.boolean"         =>  trans('has problem should be a boolean'), 
			 "has_problem.required"        =>  trans('has problem is required'), 
			 "for_all_items.boolean"       =>  trans('for_all items should be a boolean'), 
			 "for_all_items.required"      =>  trans('for_all items is required'), 
			 "items.array"                 =>  trans('items is not array'), 
			 "items.*.exists.*"            =>  trans('items is not Valid'), 
			 "items.required"              =>  trans('items is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
