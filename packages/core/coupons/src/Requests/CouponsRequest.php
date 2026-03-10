<?php

namespace Core\Coupons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CouponsRequest extends FormRequest
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
			 "translations.en.title"  =>  ['required','string'], 
			 "translations.ar.title"  =>  ['required','string'], 
			 "status"                 =>  ['required','in:active,not-active'], 
			 "applying"               =>  ['required','in:auto,manual'], 
			 "code"                   =>  ['nullable','string'], 
			 "max_use"                =>  ['nullable','numeric'], 
			 "max_use_per_user"       =>  ['nullable','numeric'], 
			 "payment_method"         =>  ['nullable','in:cash,card'], 
			 "start_at"               =>  ['nullable','date'], 
			 "end_at"                 =>  ['nullable','date'], 
			 "order_type"             =>  ['nullable','in:clothes,sales,services,maid,host'], 
			 "all_products"           =>  ['nullable','boolean'], 
			 "products"               =>  ['nullable','array'], 
			 "products.*"             =>  ['nullable','exists:products,id'], 
			 "categories"             =>  ['nullable','array'], 
			 "categories.*"           =>  ['nullable','exists:categories,id'], 
			 "all_users"              =>  ['nullable','boolean'], 
			 "users"                  =>  ['nullable','array'], 
			 "users.*"                =>  ['nullable','exists:users,id'], 
			 "roles"                  =>  ['nullable','array'], 
			 "roles.*"                =>  ['nullable','exists:roles,id'], 
			 "order_minimum"          =>  ['nullable','numeric'], 
			 "order_maximum"          =>  ['nullable','numeric'], 
			 "type"                   =>  ['required','in:value,percentage,cashback'], 
			 "value"                  =>  ['required','numeric'], 
			 "max_value"              =>  ['nullable','numeric'], 
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
			 "translations.en.title.string"    =>  trans('title should be a string'), 
			 "translations.en.title.required"  =>  trans('title is required'), 
			 "translations.ar.title.string"    =>  trans('title should be a string'), 
			 "translations.ar.title.required"  =>  trans('title is required'), 
			 "status.in"                       =>  trans('status is not allowed'), 
			 "status.required"                 =>  trans('status is required'), 
			 "applying.in"                     =>  trans('applying is not allowed'), 
			 "applying.required"               =>  trans('applying is required'), 
			 "code.string"                     =>  trans('code should be a string'), 
			 "code.required"                   =>  trans('code is required'), 
			 "max_use.numeric"                 =>  trans('max use should be a number'), 
			 "max_use.required"                =>  trans('max use is required'), 
			 "max_use_per_user.numeric"        =>  trans('max use per user should be a number'), 
			 "max_use_per_user.required"       =>  trans('max use per user is required'), 
			 "payment_method.in"               =>  trans('payment method is not allowed'), 
			 "payment_method.required"         =>  trans('payment method is required'), 
			 "start_at.date"                   =>  trans('start at should be a date'), 
			 "start_at.required"               =>  trans('start at is required'), 
			 "end_at.date"                     =>  trans('end at should be a date'), 
			 "end_at.required"                 =>  trans('end at is required'), 
			 "order_type.in"                   =>  trans('order Type is not allowed'), 
			 "order_type.required"             =>  trans('order Type is required'), 
			 "all_products.boolean"            =>  trans('all products should be a boolean'), 
			 "all_products.required"           =>  trans('all products is required'), 
			 "products.array"                  =>  trans('products is not array'), 
			 "products.*.exists.*"             =>  trans('products is not Valid'), 
			 "products.required"               =>  trans('products is required'), 
			 "categories.array"                =>  trans('categories is not array'), 
			 "categories.*.exists.*"           =>  trans('categories is not Valid'), 
			 "categories.required"             =>  trans('categories is required'), 
			 "all_users.boolean"               =>  trans('all users should be a boolean'), 
			 "all_users.required"              =>  trans('all users is required'), 
			 "users.array"                     =>  trans('users is not array'), 
			 "users.*.exists.*"                =>  trans('users is not Valid'), 
			 "users.required"                  =>  trans('users is required'), 
			 "roles.array"                     =>  trans('roles is not array'), 
			 "roles.*.exists.*"                =>  trans('roles is not Valid'), 
			 "roles.required"                  =>  trans('roles is required'), 
			 "order_minimum.numeric"           =>  trans('order minimum should be a number'), 
			 "order_minimum.required"          =>  trans('order minimum is required'), 
			 "order_maximum.numeric"           =>  trans('order maximum should be a number'), 
			 "order_maximum.required"          =>  trans('order maximum is required'), 
			 "type.in"                         =>  trans('type is not allowed'), 
			 "type.required"                   =>  trans('type is required'), 
			 "value.numeric"                   =>  trans('value should be a number'), 
			 "value.required"                  =>  trans('value is required'), 
			 "max_value.numeric"               =>  trans('max value should be a number'), 
			 "max_value.required"              =>  trans('max value is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
