<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderItemQtyUpdatesRequest extends FormRequest
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
			 "item_id"        =>  ['required','exists:order_items,id'], 
			 "from"           =>  ['nullable','numeric'], 
			 "to"             =>  ['required','numeric'], 
			 "updater_email"  =>  ['nullable','email'], 
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
			 "item_id.exists"          =>  trans('item is not Valid'), 
			 "item_id.required"        =>  trans('item is required'), 
			 "from.numeric"            =>  trans('from should be a number'), 
			 "from.required"           =>  trans('from is required'), 
			 "to.numeric"              =>  trans('to should be a number'), 
			 "to.required"             =>  trans('to is required'), 
			 "updater_email.email"     =>  trans('updater email should be a email'), 
			 "updater_email.required"  =>  trans('updater email is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
