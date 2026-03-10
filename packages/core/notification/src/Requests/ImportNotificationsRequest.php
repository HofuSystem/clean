<?php

namespace Core\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportNotificationsRequest extends FormRequest
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
			 "data.*.types"      =>  ['required','array'], 
			 "data.*.types.*"    =>  ['in:apps,email,sms,whatsapp'], 
			 "data.*.for"        =>  ['required','in:all,users,email,phone'], 
			 "data.*.title"      =>  ['required','string'], 
			 "data.*.sender_id"  =>  ['nullable','exists:users,id'], 
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
			 "data.*.types.array"         =>  trans('types is not array'), 
			 "data.*.types.*.in.*"        =>  trans('types is not allowed'), 
			 "data.*.types.required"      =>  trans('types is required'), 
			 "data.*.for.in"              =>  trans('for is not allowed'), 
			 "data.*.for.required"        =>  trans('for is required'), 
			 "data.*.title.string"        =>  trans('title should be a string'), 
			 "data.*.title.required"      =>  trans('title is required'), 
			 "data.*.sender_id.exists"    =>  trans('sender is not Valid'), 
			 "data.*.sender_id.required"  =>  trans('sender is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
