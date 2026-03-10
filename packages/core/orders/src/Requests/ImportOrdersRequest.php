<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportOrdersRequest extends FormRequest
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
			 "data.*.reference_id"                         =>  ['required','unique:orders,reference_id','string'], 
			 "data.*.type"                                 =>  ['required','in:services,clothes,sales,maid,host'], 
			 "data.*.status"                               =>  ['nullable','in:pending,issue,ready_to_delivered,finished'], 
			 "data.*.client_id"                            =>  ['required','exists:users,id'], 
			 "data.*.pay_type"                             =>  ['nullable','in:card,cash'], 
			 "data.*.transaction_id"                       =>  ['nullable','string'], 
			 "data.*.days_per_week"                        =>  ['nullable','numeric'], 
			 "data.*.days_per_week_names"                  =>  ['nullable','array'], 
			 "data.*.days_per_week_names.*"                =>  ['in:saturday ,sunday,Monday,tuesday,wednesday,thursday,friday'], 
			 "data.*.coupon_id"                            =>  ['nullable','exists:coupons,id'], 
			 "data.*.order_price"                          =>  ['required','numeric'], 
			 "data.*.delivery_price"                       =>  ['nullable','numeric'], 
			 "data.*.total_price"                          =>  ['nullable','numeric'], 
			 "data.*.paid"                                 =>  ['nullable','numeric'], 
			 "data.*.is_admin_accepted"                    =>  ['nullable','boolean'], 
			 "data.*.wallet_used"                          =>  ['nullable','boolean'], 
			 "data.*.wallet_amount_used"                   =>  ['nullable','numeric'], 
			 "data.*.items"                                =>  ['nullable','array'], 
			 "data.*.items.*.product_id"                   =>  ['nullable','exists:products,id'], 
			 "data.*.items.*.product_price"                =>  ['nullable','numeric'], 
			 "data.*.items.*.quantity"                     =>  ['nullable','numeric'], 
			 "data.*.items.*.width"                  		=>  ['nullable','numeric'], 
			 "data.*.items.*.height"                  		=>  ['nullable','numeric'], 
			 "data.*.items.*.add_by_admin"                 =>  ['nullable','email'], 
			 "data.*.items.*.update_by_admin"              =>  ['nullable','email'], 
			 "data.*.items.*.is_picked"                    =>  ['nullable','boolean'], 
			 "data.*.items.*.is_delivered"                 =>  ['nullable','boolean'], 
			 "data.*.items.*.qtyUpdates"                   =>  ['nullable','array'], 
			 "data.*.items.*.qtyUpdates.*.from"            =>  ['nullable','numeric'], 
			 "data.*.items.*.qtyUpdates.*.to"              =>  ['required','numeric'], 
			 "data.*.items.*.qtyUpdates.*.updater_email"   =>  ['nullable','email'], 
			 "data.*.representatives"                      =>  ['nullable','array'], 
			 "data.*.representatives.*.representative_id"  =>  ['required','exists:users,id'], 
			 "data.*.representatives.*.type"               =>  ['nullable','in:delivery,technical,receiver'], 
			 "data.*.representatives.*.date"               =>  ['nullable','date'], 
			 "data.*.representatives.*.time"               =>  ['nullable','time'], 
			 "data.*.representatives.*.to_time"            =>  ['nullable','time'], 
			 "data.*.representatives.*.lat"                =>  ['nullable','string'], 
			 "data.*.representatives.*.lng"                =>  ['nullable','string'], 
			 "data.*.representatives.*.location"           =>  ['nullable','string'], 
			 "data.*.representatives.*.has_problem"        =>  ['nullable','boolean'], 
			 "data.*.representatives.*.for_all_items"      =>  ['nullable','boolean'], 
			 "data.*.representatives.*.items"              =>  ['nullable','array'], 
			 "data.*.representatives.*.items.*"            =>  ['exists:order_items,id'], 
			 "data.*.reports"                              =>  ['nullable','array'], 
			 "data.*.reports.*.user_id"                    =>  ['required','exists:users,id'], 
			 "data.*.reports.*.report_reason_id"           =>  ['required','exists:report_reasons,id'], 
			 "data.*.more_data"                            =>  ['nullable','array'], 
			 "data.*.more_data.*.key"                      =>  ['required','string'], 
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
			 "data.*.reference_id.string"                           =>  trans('reference_id should be a string'), 
			 "data.*.reference_id.required"                         =>  trans('reference_id is required'), 
			 "data.*.reference_id.unique"                           =>  trans('reference_id should be unique'), 
			 "data.*.type.in"                                       =>  trans('type is not allowed'), 
			 "data.*.type.required"                                 =>  trans('type is required'), 
			 "data.*.status.in"                                     =>  trans('status is not allowed'), 
			 "data.*.status.required"                               =>  trans('status is required'), 
			 "data.*.client_id.exists"                              =>  trans('client is not Valid'), 
			 "data.*.client_id.required"                            =>  trans('client is required'), 
			 "data.*.pay_type.in"                                   =>  trans('pay type is not allowed'), 
			 "data.*.pay_type.required"                             =>  trans('pay type is required'), 
			 "data.*.transaction_id.string"                         =>  trans('transaction id should be a string'), 
			 "data.*.transaction_id.required"                       =>  trans('transaction id is required'), 
			 "data.*.days_per_week.numeric"                         =>  trans('days per week should be a number'), 
			 "data.*.days_per_week.required"                        =>  trans('days per week is required'), 
			 "data.*.days_per_week_names.array"                     =>  trans('days per week names is not array'), 
			 "data.*.days_per_week_names.*.in.*"                    =>  trans('days per week names is not allowed'), 
			 "data.*.days_per_week_names.required"                  =>  trans('days per week names is required'), 
			 "data.*.coupon_id.exists"                              =>  trans('coupon is not Valid'), 
			 "data.*.coupon_id.required"                            =>  trans('coupon is required'), 
			 "data.*.order_price.numeric"                           =>  trans('order price  should be a number'), 
			 "data.*.order_price.required"                          =>  trans('order price  is required'), 
			 "data.*.delivery_price.numeric"                        =>  trans('delivery price  should be a number'), 
			 "data.*.delivery_price.required"                       =>  trans('delivery price  is required'), 
			 "data.*.total_price.numeric"                           =>  trans('total price should be a number'), 
			 "data.*.total_price.required"                          =>  trans('total price is required'), 
			 "data.*.paid.numeric"                                  =>  trans('paid should be a number'), 
			 "data.*.paid.required"                                 =>  trans('paid is required'), 
			 "data.*.is_admin_accepted.boolean"                     =>  trans('is admin accepted should be a boolean'), 
			 "data.*.is_admin_accepted.required"                    =>  trans('is admin accepted is required'), 
			 "data.*.wallet_used.boolean"                           =>  trans('wallet used should be a boolean'), 
			 "data.*.wallet_used.required"                          =>  trans('wallet used is required'), 
			 "data.*.wallet_amount_used.numeric"                    =>  trans('wallet amount used should be a number'), 
			 "data.*.wallet_amount_used.required"                   =>  trans('wallet amount used is required'), 
			 "data.*.items.array"                                   =>  trans('items is not array'), 
			 "data.*.items.*.order_id.exists"                       =>  trans('order is not Valid'), 
			 "data.*.items.*.order_id.required"                     =>  trans('order is required'), 
			 "data.*.items.*.product_id.exists"                     =>  trans('product is not Valid'), 
			 "data.*.items.*.product_id.required"                   =>  trans('product is required'), 
			 "data.*.items.*.product_price.numeric"                 =>  trans('product price should be a number'), 
			 "data.*.items.*.product_price.required"                =>  trans('product price is required'), 
			 "data.*.items.*.quantity.numeric"                      =>  trans('quantity should be a number'), 
			 "data.*.items.*.quantity.required"                     =>  trans('quantity is required'), 
			 "data.*.items.*.height.numeric"                   		=>  trans('height should be a number'), 
			 "data.*.items.*.height.required"                  		=>  trans('height is required'), 
			 "data.*.items.*.width.numeric"                   		=>  trans('width should be a number'), 
			 "data.*.items.*.width.required"                  		=>  trans('width size is required'), 
			 "data.*.items.*.add_by_admin.email"                    =>  trans('add by admin should be a email'), 
			 "data.*.items.*.add_by_admin.required"                 =>  trans('add by admin is required'), 
			 "data.*.items.*.update_by_admin.email"                 =>  trans('update by admin should be a email'), 
			 "data.*.items.*.update_by_admin.required"              =>  trans('update by admin is required'), 
			 "data.*.items.*.is_picked.boolean"                     =>  trans('is picked should be a boolean'), 
			 "data.*.items.*.is_picked.required"                    =>  trans('is picked is required'), 
			 "data.*.items.*.is_delivered.boolean"                  =>  trans('is delivered should be a boolean'), 
			 "data.*.items.*.is_delivered.required"                 =>  trans('is delivered is required'), 
			 "data.*.items.*.qtyUpdates.array"                      =>  trans('qtyUpdates is not array'), 
			 "data.*.items.*.qtyUpdates.*.item_id.exists"           =>  trans('item is not Valid'), 
			 "data.*.items.*.qtyUpdates.*.item_id.required"         =>  trans('item is required'), 
			 "data.*.items.*.qtyUpdates.*.from.numeric"             =>  trans('from should be a number'), 
			 "data.*.items.*.qtyUpdates.*.from.required"            =>  trans('from is required'), 
			 "data.*.items.*.qtyUpdates.*.to.numeric"               =>  trans('to should be a number'), 
			 "data.*.items.*.qtyUpdates.*.to.required"              =>  trans('to is required'), 
			 "data.*.items.*.qtyUpdates.*.updater_email.email"      =>  trans('updater email should be a email'), 
			 "data.*.items.*.qtyUpdates.*.updater_email.required"   =>  trans('updater email is required'), 
			 "data.*.items.*.qtyUpdates.required"                   =>  trans('qtyUpdates is required'), 
			 "data.*.items.required"                                =>  trans('items is required'), 
			 "data.*.representatives.array"                         =>  trans('representatives is not array'), 
			 "data.*.representatives.*.order_id.exists"             =>  trans('order is not Valid'), 
			 "data.*.representatives.*.order_id.required"           =>  trans('order is required'), 
			 "data.*.representatives.*.representative_id.exists"    =>  trans('representative is not Valid'), 
			 "data.*.representatives.*.representative_id.required"  =>  trans('representative is required'), 
			 "data.*.representatives.*.type.in"                     =>  trans('type is not allowed'), 
			 "data.*.representatives.*.type.required"               =>  trans('type is required'), 
			 "data.*.representatives.*.date.date"                   =>  trans('date should be a date'), 
			 "data.*.representatives.*.date.required"               =>  trans('date is required'), 
			 "data.*.representatives.*.time.time"                   =>  trans('time should be a time'), 
			 "data.*.representatives.*.time.required"               =>  trans('time is required'), 
			 "data.*.representatives.*.to_time.time"                =>  trans('to time should be a time'), 
			 "data.*.representatives.*.to_time.required"            =>  trans('to time is required'), 
			 "data.*.representatives.*.lat.string"                  =>  trans('lat should be a string'), 
			 "data.*.representatives.*.lat.required"                =>  trans('lat is required'), 
			 "data.*.representatives.*.lng.string"                  =>  trans('lng should be a string'), 
			 "data.*.representatives.*.lng.required"                =>  trans('lng is required'), 
			 "data.*.representatives.*.location.string"             =>  trans('location should be a string'), 
			 "data.*.representatives.*.location.required"           =>  trans('location is required'), 
			 "data.*.representatives.*.has_problem.boolean"         =>  trans('has problem should be a boolean'), 
			 "data.*.representatives.*.has_problem.required"        =>  trans('has problem is required'), 
			 "data.*.representatives.*.for_all_items.boolean"       =>  trans('for_all items should be a boolean'), 
			 "data.*.representatives.*.for_all_items.required"      =>  trans('for_all items is required'), 
			 "data.*.representatives.*.items.array"                 =>  trans('items is not array'), 
			 "data.*.representatives.*.items.*.exists.*"            =>  trans('items is not Valid'), 
			 "data.*.representatives.*.items.required"              =>  trans('items is required'), 
			 "data.*.representatives.required"                      =>  trans('representatives is required'), 
			 "data.*.reports.array"                                 =>  trans('reports is not array'), 
			 "data.*.reports.*.order_id.exists"                     =>  trans('order is not Valid'), 
			 "data.*.reports.*.order_id.required"                   =>  trans('order is required'), 
			 "data.*.reports.*.user_id.exists"                      =>  trans('user is not Valid'), 
			 "data.*.reports.*.user_id.required"                    =>  trans('user is required'), 
			 "data.*.reports.*.report_reason_id.exists"             =>  trans('report reason is not Valid'), 
			 "data.*.reports.*.report_reason_id.required"           =>  trans('report reason is required'), 
			 "data.*.reports.required"                              =>  trans('reports is required'), 
			 "data.*.more_data.array"                               =>  trans('more Data is not array'), 
			 "data.*.more_data.*.order_id.exists"                   =>  trans('order is not Valid'), 
			 "data.*.more_data.*.order_id.required"                 =>  trans('order is required'), 
			 "data.*.more_data.*.key.string"                        =>  trans('key should be a string'), 
			 "data.*.more_data.*.key.required"                      =>  trans('key is required'), 
			 "data.*.more_data.required"                            =>  trans('more Data is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
