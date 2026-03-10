<?php

namespace Core\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class NotificationsRequest extends FormRequest
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
			  "types"         =>  ['required','array'], 
			  "types.*"       =>  ['in:apps,email,sms,whats_app'], 
			  "for"           =>  ['required','in:all,users,email,phone'], 
			  "title"         =>  ['required','string'], 
			  "body"          =>  ['required','string'], 
			  "sender_id"     =>  ['nullable','exists:users,id'], 
        "register_from" => ['nullable','date'],
        "register_to"   => ['nullable','date'],
        "orders_from"   => ['nullable','date'],
        "orders_to"     => ['nullable','date'],
        "orders_min"    => ['nullable','integer'],
        "orders_max"    => ['nullable','integer']
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
			 "types.array"         =>  trans('types is not array'), 
			 "types.*.in.*"        =>  trans('types is not allowed'), 
			 "types.required"      =>  trans('types is required'), 
			 "for.in"              =>  trans('for is not allowed'), 
			 "for.required"        =>  trans('for is required'), 
			 "title.string"        =>  trans('title should be a string'), 
			 "title.required"      =>  trans('title is required'), 
			 "sender_id.exists"    =>  trans('sender is not Valid'), 
			 "sender_id.required"  =>  trans('sender is required'),
        
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
