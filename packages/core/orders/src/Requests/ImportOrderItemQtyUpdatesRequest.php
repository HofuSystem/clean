<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderItemQtyUpdatesRequest extends FormRequest
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
			 "data.*.item_id"        =>  ['required','exists:order_items,id'], 
			 "data.*.from"           =>  ['nullable','numeric'], 
			 "data.*.to"             =>  ['required','numeric'], 
			 "data.*.updater_email"  =>  ['nullable','email'], 
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
			 "data.*.item_id.exists"          =>  trans('item is not Valid'), 
			 "data.*.item_id.required"        =>  trans('item is required'), 
			 "data.*.from.numeric"            =>  trans('from should be a number'), 
			 "data.*.from.required"           =>  trans('from is required'), 
			 "data.*.to.numeric"              =>  trans('to should be a number'), 
			 "data.*.to.required"             =>  trans('to is required'), 
			 "data.*.updater_email.email"     =>  trans('updater email should be a email'), 
			 "data.*.updater_email.required"  =>  trans('updater email is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
