<?php

namespace Core\Coupons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCouponsRequest extends FormRequest
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
			 "data.*.translations.en.title"  =>  ['required','string'], 
			 "data.*.translations.ar.title"  =>  ['required','string'], 
			 "data.*.status"                 =>  ['required','in:active,not-active'], 
			 "data.*.applying"               =>  ['required','in:auto,manual'], 
			 "data.*.code"                   =>  ['nullable','string'], 
			 "data.*.max_use"                =>  ['nullable','numeric'], 
			 "data.*.max_use_per_user"       =>  ['nullable','numeric'], 
			 "data.*.payment_method"         =>  ['nullable','in:cash,card'], 
			 "data.*.start_at"               =>  ['nullable','date'], 
			 "data.*.end_at"                 =>  ['nullable','date'], 
			 "data.*.order_type"             =>  ['nullable','in:clothes,sales,services,maid,host'], 
			 "data.*.all_products"           =>  ['nullable','boolean'], 
			 "data.*.products"               =>  ['nullable','array'], 
			 "data.*.products.*"             =>  ['exists:products,id'], 
			 "data.*.categories"             =>  ['nullable','array'], 
			 "data.*.categories.*"           =>  ['exists:categories,id'], 
			 "data.*.all_users"              =>  ['nullable','boolean'], 
			 "data.*.users"                  =>  ['nullable','array'], 
			 "data.*.users.*"                =>  ['exists:users,id'], 
			 "data.*.roles"                  =>  ['nullable','array'], 
			 "data.*.roles.*"                =>  ['exists:roles,id'], 
			 "data.*.order_minimum"          =>  ['nullable','numeric'], 
			 "data.*.order_maximum"          =>  ['nullable','numeric'], 
			 "data.*.type"                   =>  ['required','in:value,percentage'], 
			 "data.*.value"                  =>  ['required','numeric'], 
			 "data.*.max_value"              =>  ['nullable','numeric'], 
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
			 "data.*.translations.en.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.en.title.required"  =>  trans('title is required'), 
			 "data.*.translations.ar.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.ar.title.required"  =>  trans('title is required'), 
			 "data.*.status.in"                       =>  trans('status is not allowed'), 
			 "data.*.status.required"                 =>  trans('status is required'), 
			 "data.*.applying.in"                     =>  trans('applying is not allowed'), 
			 "data.*.applying.required"               =>  trans('applying is required'), 
			 "data.*.code.string"                     =>  trans('code should be a string'), 
			 "data.*.code.required"                   =>  trans('code is required'), 
			 "data.*.max_use.numeric"                 =>  trans('max use should be a number'), 
			 "data.*.max_use.required"                =>  trans('max use is required'), 
			 "data.*.max_use_per_user.numeric"        =>  trans('max use per user should be a number'), 
			 "data.*.max_use_per_user.required"       =>  trans('max use per user is required'), 
			 "data.*.payment_method.in"               =>  trans('payment method is not allowed'), 
			 "data.*.payment_method.required"         =>  trans('payment method is required'), 
			 "data.*.start_at.date"                   =>  trans('start at should be a date'), 
			 "data.*.start_at.required"               =>  trans('start at is required'), 
			 "data.*.end_at.date"                     =>  trans('end at should be a date'), 
			 "data.*.end_at.required"                 =>  trans('end at is required'), 
			 "data.*.order_type.in"                   =>  trans('order Type is not allowed'), 
			 "data.*.order_type.required"             =>  trans('order Type is required'), 
			 "data.*.all_products.boolean"            =>  trans('all products should be a boolean'), 
			 "data.*.all_products.required"           =>  trans('all products is required'), 
			 "data.*.products.array"                  =>  trans('products is not array'), 
			 "data.*.products.*.exists.*"             =>  trans('products is not Valid'), 
			 "data.*.products.required"               =>  trans('products is required'), 
			 "data.*.categories.array"                =>  trans('categories is not array'), 
			 "data.*.categories.*.exists.*"           =>  trans('categories is not Valid'), 
			 "data.*.categories.required"             =>  trans('categories is required'), 
			 "data.*.all_users.boolean"               =>  trans('all users should be a boolean'), 
			 "data.*.all_users.required"              =>  trans('all users is required'), 
			 "data.*.users.array"                     =>  trans('users is not array'), 
			 "data.*.users.*.exists.*"                =>  trans('users is not Valid'), 
			 "data.*.users.required"                  =>  trans('users is required'), 
			 "data.*.roles.array"                     =>  trans('roles is not array'), 
			 "data.*.roles.*.exists.*"                =>  trans('roles is not Valid'), 
			 "data.*.roles.required"                  =>  trans('roles is required'), 
			 "data.*.order_minimum.numeric"           =>  trans('order minimum should be a number'), 
			 "data.*.order_minimum.required"          =>  trans('order minimum is required'), 
			 "data.*.order_maximum.numeric"           =>  trans('order maximum should be a number'), 
			 "data.*.order_maximum.required"          =>  trans('order maximum is required'), 
			 "data.*.type.in"                         =>  trans('type is not allowed'), 
			 "data.*.type.required"                   =>  trans('type is required'), 
			 "data.*.value.numeric"                   =>  trans('value should be a number'), 
			 "data.*.value.required"                  =>  trans('value is required'), 
			 "data.*.max_value.numeric"               =>  trans('max value should be a number'), 
			 "data.*.max_value.required"              =>  trans('max value is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
