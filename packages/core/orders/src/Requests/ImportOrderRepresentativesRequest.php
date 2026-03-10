<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderRepresentativesRequest extends FormRequest
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
			 "data.*.order_id"           =>  ['required','exists:orders,id'], 
			 "data.*.representative_id"  =>  ['required','exists:users,id'], 
			 "data.*.type"               =>  ['nullable','in:delivery,technical,receiver'], 
			 "data.*.date"               =>  ['nullable','date'], 
			 "data.*.time"               =>  ['nullable','time'], 
			 "data.*.to_time"            =>  ['nullable','time'], 
			 "data.*.lat"                =>  ['nullable','string'], 
			 "data.*.lng"                =>  ['nullable','string'], 
			 "data.*.location"           =>  ['nullable','string'], 
			 "data.*.has_problem"        =>  ['nullable','boolean'], 
			 "data.*.for_all_items"      =>  ['nullable','boolean'], 
			 "data.*.items"              =>  ['nullable','array'], 
			 "data.*.items.*"            =>  ['exists:order_items,id'], 
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
			 "data.*.order_id.exists"             =>  trans('order is not Valid'), 
			 "data.*.order_id.required"           =>  trans('order is required'), 
			 "data.*.representative_id.exists"    =>  trans('representative is not Valid'), 
			 "data.*.representative_id.required"  =>  trans('representative is required'), 
			 "data.*.type.in"                     =>  trans('type is not allowed'), 
			 "data.*.type.required"               =>  trans('type is required'), 
			 "data.*.date.date"                   =>  trans('date should be a date'), 
			 "data.*.date.required"               =>  trans('date is required'), 
			 "data.*.time.time"                   =>  trans('time should be a time'), 
			 "data.*.time.required"               =>  trans('time is required'), 
			 "data.*.to_time.time"                =>  trans('to time should be a time'), 
			 "data.*.to_time.required"            =>  trans('to time is required'), 
			 "data.*.lat.string"                  =>  trans('lat should be a string'), 
			 "data.*.lat.required"                =>  trans('lat is required'), 
			 "data.*.lng.string"                  =>  trans('lng should be a string'), 
			 "data.*.lng.required"                =>  trans('lng is required'), 
			 "data.*.location.string"             =>  trans('location should be a string'), 
			 "data.*.location.required"           =>  trans('location is required'), 
			 "data.*.has_problem.boolean"         =>  trans('has problem should be a boolean'), 
			 "data.*.has_problem.required"        =>  trans('has problem is required'), 
			 "data.*.for_all_items.boolean"       =>  trans('for_all items should be a boolean'), 
			 "data.*.for_all_items.required"      =>  trans('for_all items is required'), 
			 "data.*.items.array"                 =>  trans('items is not array'), 
			 "data.*.items.*.exists.*"            =>  trans('items is not Valid'), 
			 "data.*.items.required"              =>  trans('items is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
