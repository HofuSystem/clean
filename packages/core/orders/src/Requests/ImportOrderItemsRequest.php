<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrderItemsRequest extends FormRequest
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
			 "data.*.order_id"                    =>  ['required','exists:orders,id'], 
			 "data.*.product_id"                  =>  ['nullable','exists:products,id'], 
			 "data.*.product_price"               =>  ['nullable','numeric'], 
			 "data.*.quantity"                    =>  ['nullable','numeric'], 
			 "data.*.width"                 	  =>  ['nullable','numeric'], 
			 "data.*.height"                 	  =>  ['nullable','numeric'], 
			 "data.*.add_by_admin"                =>  ['nullable','email'], 
			 "data.*.update_by_admin"             =>  ['nullable','email'], 
			 "data.*.is_picked"                   =>  ['nullable','boolean'], 
			 "data.*.is_delivered"                =>  ['nullable','boolean'], 
			 "data.*.qtyUpdates"                  =>  ['nullable','array'], 
			 "data.*.qtyUpdates.*.from"           =>  ['nullable','numeric'], 
			 "data.*.qtyUpdates.*.to"             =>  ['required','numeric'], 
			 "data.*.qtyUpdates.*.updater_email"  =>  ['nullable','email'], 
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
			 "data.*.order_id.exists"                      =>  trans('order is not Valid'), 
			 "data.*.order_id.required"                    =>  trans('order is required'), 
			 "data.*.product_id.exists"                    =>  trans('product is not Valid'), 
			 "data.*.product_id.required"                  =>  trans('product is required'), 
			 "data.*.product_price.numeric"                =>  trans('product price should be a number'), 
			 "data.*.product_price.required"               =>  trans('product price is required'), 
			 "data.*.quantity.numeric"                     =>  trans('quantity should be a number'), 
			 "data.*.quantity.required"                    =>  trans('quantity is required'), 
			 "data.*.width.numeric"                  	   =>  trans('width should be a number'), 
			 "data.*.height.numeric"                  	   =>  trans('height should be a number'), 
			 "data.*.width.required"                 	   =>  trans('width is required'), 
			 "data.*.height.required"                 	   =>  trans('width is required'), 
			 "data.*.add_by_admin.email"                   =>  trans('add by admin should be a email'), 
			 "data.*.add_by_admin.required"                =>  trans('add by admin is required'), 
			 "data.*.update_by_admin.email"                =>  trans('update by admin should be a email'), 
			 "data.*.update_by_admin.required"             =>  trans('update by admin is required'), 
			 "data.*.is_picked.boolean"                    =>  trans('is picked should be a boolean'), 
			 "data.*.is_picked.required"                   =>  trans('is picked is required'), 
			 "data.*.is_delivered.boolean"                 =>  trans('is delivered should be a boolean'), 
			 "data.*.is_delivered.required"                =>  trans('is delivered is required'), 
			 "data.*.qtyUpdates.array"                     =>  trans('qtyUpdates is not array'), 
			 "data.*.qtyUpdates.*.item_id.exists"          =>  trans('item is not Valid'), 
			 "data.*.qtyUpdates.*.item_id.required"        =>  trans('item is required'), 
			 "data.*.qtyUpdates.*.from.numeric"            =>  trans('from should be a number'), 
			 "data.*.qtyUpdates.*.from.required"           =>  trans('from is required'), 
			 "data.*.qtyUpdates.*.to.numeric"              =>  trans('to should be a number'), 
			 "data.*.qtyUpdates.*.to.required"             =>  trans('to is required'), 
			 "data.*.qtyUpdates.*.updater_email.email"     =>  trans('updater email should be a email'), 
			 "data.*.qtyUpdates.*.updater_email.required"  =>  trans('updater email is required'), 
			 "data.*.qtyUpdates.required"                  =>  trans('qtyUpdates is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
