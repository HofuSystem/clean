<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrderItemsRequest extends FormRequest
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
			 "order_id"                    =>  ['required','exists:orders,id'], 
			 "product_id"                  =>  ['nullable','exists:products,id'], 
			 "product_price"               =>  ['nullable','numeric'], 
			 "quantity"                    =>  ['nullable','numeric'], 
			 "width"                 		=>  ['nullable','numeric'], 
			 "height"                	 	=>  ['nullable','numeric'], 
			 "add_by_admin"                =>  ['nullable','email'], 
			 "update_by_admin"             =>  ['nullable','email'], 
			 "is_picked"                   =>  ['nullable','boolean'], 
			 "is_delivered"                =>  ['nullable','boolean'], 
			 "qtyUpdates"                  =>  ['nullable','array'], 
			 "qtyUpdates.*.from"           =>  ['nullable','numeric'], 
			 "qtyUpdates.*.to"             =>  ['required','numeric'], 
			 "qtyUpdates.*.updater_email"  =>  ['nullable','email'], 
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
			 "order_id.exists"                      =>  trans('order is not Valid'), 
			 "order_id.required"                    =>  trans('order is required'), 
			 "product_id.exists"                    =>  trans('product is not Valid'), 
			 "product_id.required"                  =>  trans('product is required'), 
			 "product_price.numeric"                =>  trans('product price should be a number'), 
			 "product_price.required"               =>  trans('product price is required'), 
			 "quantity.numeric"                     =>  trans('quantity should be a number'), 
			 "quantity.required"                    =>  trans('quantity is required'), 
			 "width.numeric"                  		=>  trans('width should be a number'), 
			 "width.required"                 		=>  trans('width is required'), 
			 "height.numeric"                  		=>  trans('height should be a number'), 
			 "height.required"                 		=>  trans('height is required'), 
			 "add_by_admin.email"                   =>  trans('add by admin should be a email'), 
			 "add_by_admin.required"                =>  trans('add by admin is required'), 
			 "update_by_admin.email"                =>  trans('update by admin should be a email'), 
			 "update_by_admin.required"             =>  trans('update by admin is required'), 
			 "is_picked.boolean"                    =>  trans('is picked should be a boolean'), 
			 "is_picked.required"                   =>  trans('is picked is required'), 
			 "is_delivered.boolean"                 =>  trans('is delivered should be a boolean'), 
			 "is_delivered.required"                =>  trans('is delivered is required'), 
			 "qtyUpdates.array"                     =>  trans('qtyUpdates is not array'), 
			 "qtyUpdates.*.item_id.exists"          =>  trans('item is not Valid'), 
			 "qtyUpdates.*.item_id.required"        =>  trans('item is required'), 
			 "qtyUpdates.*.from.numeric"            =>  trans('from should be a number'), 
			 "qtyUpdates.*.from.required"           =>  trans('from is required'), 
			 "qtyUpdates.*.to.numeric"              =>  trans('to should be a number'), 
			 "qtyUpdates.*.to.required"             =>  trans('to is required'), 
			 "qtyUpdates.*.updater_email.email"     =>  trans('updater email should be a email'), 
			 "qtyUpdates.*.updater_email.required"  =>  trans('updater email is required'), 
			 "qtyUpdates.required"                  =>  trans('qtyUpdates is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
