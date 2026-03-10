<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoriesRequest extends FormRequest
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
			 "slug"                                                                           =>  ['required','unique:categories,slug,'.$this->id,'string'], 
			 "translations.en.name"                                                           =>  ['required','string'], 
			 "translations.ar.name"                                                           =>  ['required','string'], 
			 "type"                                                                           =>  ['required','in:clothes,sales,services,maid,host'], 
			 "delivery_price"                                                                 =>  ['nullable','numeric'], 
			 "sort"                                                                           =>  ['nullable','numeric'], 
			 "is_package"                                                                     =>  ['nullable','boolean'], 
			 "status"                                                                         =>  ['nullable','in:active,not-active'], 
			 "parent_id"                                                                      =>  ['nullable','exists:categories,id'], 
			 "cities"                                                                         =>  ['array'], 
			 "cities.*"                                                                       =>  ['nullable','exists:cities,id'], 
			 "sub_categories"                                                                 =>  ['nullable','array'], 
			 "sub_categories.*.translations.en.name"                                          =>  ['required','string'], 
			 "sub_categories.*.translations.ar.name"                                          =>  ['required','string'], 
			 "sub_categories.*.type"                                                          =>  ['required','in:clothes,sales,services,maid,host'], 
			 "sub_categories.*.delivery_price"                                                =>  ['nullable','numeric'], 
			 "sub_categories.*.sort"                                                          =>  ['nullable','numeric'], 
			 "sub_categories.*.is_package"                                                    =>  ['nullable','boolean'], 
			 "sub_categories.*.status"                                                        =>  ['nullable','in:active,not-active'], 
			 "sub_categories.*.cities"                                                        =>  ['array'], 
			 "sub_categories.*.cities.*"                                                      =>  ['nullable','exists:cities,id'], 
			 "sub_categories.*.category_types"                                                =>  ['nullable','array'], 
			 "sub_categories.*.category_types.*.translations.en.name"                         =>  ['required','string'], 
			 "sub_categories.*.category_types.*.translations.ar.name"                         =>  ['required','string'], 
			 "sub_categories.*.category_types.*.hour_price"                                   =>  ['required','numeric'], 
			 "sub_categories.*.category_types.*.status"                                       =>  ['required','in:active,not-active'], 
			 "sub_categories.*.category_settings"                                             =>  ['nullable','array'], 
			 "sub_categories.*.category_settings.*.translations.en.name"                      =>  ['required','string'], 
			 "sub_categories.*.category_settings.*.translations.ar.name"                      =>  ['required','string'], 
			 "sub_categories.*.category_settings.*.category_id"                               =>  ['required','exists:categories,id'], 
			 "sub_categories.*.category_settings.*.addon_price"                               =>  ['nullable','numeric'], 
			 "sub_categories.*.category_settings.*.status"                                    =>  ['required','in:active,not-active'], 
			 "sub_categories.*.category_settings.*.category_settings"                         =>  ['nullable','array'], 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.en.name"  =>  ['required','string'], 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.ar.name"  =>  ['required','string'], 
			 "sub_categories.*.category_settings.*.category_settings.*.category_id"           =>  ['required','exists:categories,id'], 
			 "sub_categories.*.category_settings.*.category_settings.*.addon_price"           =>  ['nullable','numeric'], 
			 "sub_categories.*.category_settings.*.category_settings.*.status"                =>  ['required','in:active,not-active'], 
			 "sub_categories.*.date_times"                                                    =>  ['nullable','array'], 
			 "sub_categories.*.date_times.*.date"                                             =>  ['required','date'], 
			 "sub_categories.*.date_times.*.from"                                             =>  ['required','time'], 
			 "sub_categories.*.date_times.*.to"                                               =>  ['required','time'], 
			 "sub_categories.*.date_times.*.order_count"                                      =>  ['nullable','numeric'], 
			 "category_types"                                                                 =>  ['nullable','array'], 
			 "category_types.*.translations.en.name"                                          =>  ['required','string'], 
			 "category_types.*.translations.ar.name"                                          =>  ['required','string'], 
			 "category_types.*.hour_price"                                                    =>  ['required','numeric'], 
			 "category_types.*.status"                                                        =>  ['required','in:active,not-active'], 
			 "category_settings"                                                              =>  ['nullable','array'], 
			 "category_settings.*.translations.en.name"                                       =>  ['required','string'], 
			 "category_settings.*.translations.ar.name"                                       =>  ['required','string'], 
			 "category_settings.*.category_id"                                                =>  ['required','exists:categories,id'], 
			 "category_settings.*.addon_price"                                                =>  ['nullable','numeric'], 
			 "category_settings.*.status"                                                     =>  ['required','in:active,not-active'], 
			 "category_settings.*.category_settings"                                          =>  ['nullable','array'], 
			 "category_settings.*.category_settings.*.translations.en.name"                   =>  ['required','string'], 
			 "category_settings.*.category_settings.*.translations.ar.name"                   =>  ['required','string'], 
			 "category_settings.*.category_settings.*.category_id"                            =>  ['required','exists:categories,id'], 
			 "category_settings.*.category_settings.*.addon_price"                            =>  ['nullable','numeric'], 
			 "category_settings.*.category_settings.*.status"                                 =>  ['required','in:active,not-active'], 
			 "date_times"                                                                     =>  ['nullable','array'], 
			 "date_times.*.date"                                                              =>  ['required','date'], 
			 "date_times.*.from"                                                              =>  ['required','time'], 
			 "date_times.*.to"                                                                =>  ['required','time'], 
			 "date_times.*.order_count"                                                       =>  ['nullable','numeric'], 
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
		
			 "translations.en.name.string"                                                             =>  trans('name should be a string'), 
			 "translations.en.name.required"                                                           =>  trans('name is required'), 
			 "translations.ar.name.string"                                                             =>  trans('name should be a string'), 
			 "translations.ar.name.required"                                                           =>  trans('name is required'), 
			 "type.in"                                                                                 =>  trans('type is not allowed'), 
			 "type.required"                                                                           =>  trans('type is required'), 
			 "delivery_price.numeric"                                                                  =>  trans('delivery price should be a number'), 
			 "delivery_price.required"                                                                 =>  trans('delivery price is required'), 
			 "sort.numeric"                                                                            =>  trans('sort should be a number'), 
			 "sort.required"                                                                           =>  trans('sort is required'), 
			 "is_package.boolean"                                                                      =>  trans('is package should be a boolean'), 
			 "is_package.required"                                                                     =>  trans('is package is required'), 
			 "status.in"                                                                               =>  trans('status is not allowed'), 
			 "status.required"                                                                         =>  trans('status is required'), 
			 "parent_id.exists"                                                                        =>  trans('parent is not Valid'), 
			 "parent_id.required"                                                                      =>  trans('parent is required'), 
			 "cities.*.exists"                                                                          =>  trans('city is not Valid'), 
			 "cities.*.required"                                                                        =>  trans('city is required'), 
			 "sub_categories.array"                                                                    =>  trans('sub categories is not array'), 
			 "sub_categories.*.translations.en.name.string"                                            =>  trans('name should be a string'), 
			 "sub_categories.*.translations.en.name.required"                                          =>  trans('name is required'), 
			 "sub_categories.*.translations.ar.name.string"                                            =>  trans('name should be a string'), 
			 "sub_categories.*.translations.ar.name.required"                                          =>  trans('name is required'), 
			 "sub_categories.*.type.in"                                                                =>  trans('type is not allowed'), 
			 "sub_categories.*.type.required"                                                          =>  trans('type is required'), 
			 "sub_categories.*.delivery_price.numeric"                                                 =>  trans('delivery price should be a number'), 
			 "sub_categories.*.delivery_price.required"                                                =>  trans('delivery price is required'), 
			 "sub_categories.*.sort.numeric"                                                           =>  trans('sort should be a number'), 
			 "sub_categories.*.sort.required"                                                          =>  trans('sort is required'), 
			 "sub_categories.*.is_package.boolean"                                                     =>  trans('is package should be a boolean'), 
			 "sub_categories.*.is_package.required"                                                    =>  trans('is package is required'), 
			 "sub_categories.*.status.in"                                                              =>  trans('status is not allowed'), 
			 "sub_categories.*.status.required"                                                        =>  trans('status is required'), 
			 "sub_categories.*.parent_id.exists"                                                       =>  trans('parent is not Valid'), 
			 "sub_categories.*.parent_id.required"                                                     =>  trans('parent is required'), 
			 "sub_categories.*.cities.*.exists"                                                         =>  trans('city is not Valid'), 
			 "sub_categories.*.cities.*.required"                                                       =>  trans('city is required'), 
			 "sub_categories.*.category_types.array"                                                   =>  trans('category Types is not array'), 
			 "sub_categories.*.category_types.*.translations.en.name.string"                           =>  trans('name should be a string'), 
			 "sub_categories.*.category_types.*.translations.en.name.required"                         =>  trans('name is required'), 
			 "sub_categories.*.category_types.*.translations.ar.name.string"                           =>  trans('name should be a string'), 
			 "sub_categories.*.category_types.*.translations.ar.name.required"                         =>  trans('name is required'), 
			 "sub_categories.*.category_types.*.category_id.exists"                                    =>  trans('category is not Valid'), 
			 "sub_categories.*.category_types.*.category_id.required"                                  =>  trans('category is required'), 
			 "sub_categories.*.category_types.*.hour_price.numeric"                                    =>  trans('hour price should be a number'), 
			 "sub_categories.*.category_types.*.hour_price.required"                                   =>  trans('hour price is required'), 
			 "sub_categories.*.category_types.*.status.in"                                             =>  trans('status is not allowed'), 
			 "sub_categories.*.category_types.*.status.required"                                       =>  trans('status is required'), 
			 "sub_categories.*.category_types.required"                                                =>  trans('category Types is required'), 
			 "sub_categories.*.category_settings.array"                                                =>  trans('category settings is not array'), 
			 "sub_categories.*.category_settings.*.translations.en.name.string"                        =>  trans('name should be a string'), 
			 "sub_categories.*.category_settings.*.translations.en.name.required"                      =>  trans('name is required'), 
			 "sub_categories.*.category_settings.*.translations.ar.name.string"                        =>  trans('name should be a string'), 
			 "sub_categories.*.category_settings.*.translations.ar.name.required"                      =>  trans('name is required'), 
			 "sub_categories.*.category_settings.*.category_id.exists"                                 =>  trans('category is not Valid'), 
			 "sub_categories.*.category_settings.*.category_id.required"                               =>  trans('category is required'), 
			 "sub_categories.*.category_settings.*.addon_price.numeric"                                =>  trans('addon price should be a number'), 
			 "sub_categories.*.category_settings.*.addon_price.required"                               =>  trans('addon price is required'), 
			 "sub_categories.*.category_settings.*.parent_id.exists"                                   =>  trans('parent is not Valid'), 
			 "sub_categories.*.category_settings.*.parent_id.required"                                 =>  trans('parent is required'), 
			 "sub_categories.*.category_settings.*.status.in"                                          =>  trans('status is not allowed'), 
			 "sub_categories.*.category_settings.*.status.required"                                    =>  trans('status is required'), 
			 "sub_categories.*.category_settings.*.category_settings.array"                            =>  trans('category settings is not array'), 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.en.name.required"  =>  trans('name is required'), 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "sub_categories.*.category_settings.*.category_settings.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "sub_categories.*.category_settings.*.category_settings.*.category_id.exists"             =>  trans('category is not Valid'), 
			 "sub_categories.*.category_settings.*.category_settings.*.category_id.required"           =>  trans('category is required'), 
			 "sub_categories.*.category_settings.*.category_settings.*.addon_price.numeric"            =>  trans('addon price should be a number'), 
			 "sub_categories.*.category_settings.*.category_settings.*.addon_price.required"           =>  trans('addon price is required'), 
			 "sub_categories.*.category_settings.*.category_settings.*.parent_id.exists"               =>  trans('parent is not Valid'), 
			 "sub_categories.*.category_settings.*.category_settings.*.parent_id.required"             =>  trans('parent is required'), 
			 "sub_categories.*.category_settings.*.category_settings.*.status.in"                      =>  trans('status is not allowed'), 
			 "sub_categories.*.category_settings.*.category_settings.*.status.required"                =>  trans('status is required'), 
			 "sub_categories.*.category_settings.*.category_settings.required"                         =>  trans('category settings is required'), 
			 "sub_categories.*.category_settings.required"                                             =>  trans('category settings is required'), 
			 "sub_categories.*.date_times.array"                                                       =>  trans('date times is not array'), 
			 "sub_categories.*.date_times.*.category_id.exists"                                        =>  trans('category is not Valid'), 
			 "sub_categories.*.date_times.*.category_id.required"                                      =>  trans('category is required'), 
			 "sub_categories.*.date_times.*.date.date"                                                 =>  trans('date should be a date'), 
			 "sub_categories.*.date_times.*.date.required"                                             =>  trans('date is required'), 
			 "sub_categories.*.date_times.*.from.time"                                                 =>  trans('from should be a time'), 
			 "sub_categories.*.date_times.*.from.required"                                             =>  trans('from is required'), 
			 "sub_categories.*.date_times.*.to.time"                                                   =>  trans('to should be a time'), 
			 "sub_categories.*.date_times.*.to.required"                                               =>  trans('to is required'), 
			 "sub_categories.*.date_times.*.order_count.numeric"                                       =>  trans('order count should be a number'), 
			 "sub_categories.*.date_times.*.order_count.required"                                      =>  trans('order count is required'), 
			 "sub_categories.*.date_times.required"                                                    =>  trans('date times is required'), 
			 "sub_categories.required"                                                                 =>  trans('sub categories is required'), 
			 "category_types.array"                                                                    =>  trans('category Types is not array'), 
			 "category_types.*.translations.en.name.string"                                            =>  trans('name should be a string'), 
			 "category_types.*.translations.en.name.required"                                          =>  trans('name is required'), 
			 "category_types.*.translations.ar.name.string"                                            =>  trans('name should be a string'), 
			 "category_types.*.translations.ar.name.required"                                          =>  trans('name is required'), 
			 "category_types.*.category_id.exists"                                                     =>  trans('category is not Valid'), 
			 "category_types.*.category_id.required"                                                   =>  trans('category is required'), 
			 "category_types.*.hour_price.numeric"                                                     =>  trans('hour price should be a number'), 
			 "category_types.*.hour_price.required"                                                    =>  trans('hour price is required'), 
			 "category_types.*.status.in"                                                              =>  trans('status is not allowed'), 
			 "category_types.*.status.required"                                                        =>  trans('status is required'), 
			 "category_types.required"                                                                 =>  trans('category Types is required'), 
			 "category_settings.array"                                                                 =>  trans('category settings is not array'), 
			 "category_settings.*.translations.en.name.string"                                         =>  trans('name should be a string'), 
			 "category_settings.*.translations.en.name.required"                                       =>  trans('name is required'), 
			 "category_settings.*.translations.ar.name.string"                                         =>  trans('name should be a string'), 
			 "category_settings.*.translations.ar.name.required"                                       =>  trans('name is required'), 
			 "category_settings.*.category_id.exists"                                                  =>  trans('category is not Valid'), 
			 "category_settings.*.category_id.required"                                                =>  trans('category is required'), 
			 "category_settings.*.addon_price.numeric"                                                 =>  trans('addon price should be a number'), 
			 "category_settings.*.addon_price.required"                                                =>  trans('addon price is required'), 
			 "category_settings.*.parent_id.exists"                                                    =>  trans('parent is not Valid'), 
			 "category_settings.*.parent_id.required"                                                  =>  trans('parent is required'), 
			 "category_settings.*.status.in"                                                           =>  trans('status is not allowed'), 
			 "category_settings.*.status.required"                                                     =>  trans('status is required'), 
			 "category_settings.*.category_settings.array"                                             =>  trans('category settings is not array'), 
			 "category_settings.*.category_settings.*.translations.en.name.string"                     =>  trans('name should be a string'), 
			 "category_settings.*.category_settings.*.translations.en.name.required"                   =>  trans('name is required'), 
			 "category_settings.*.category_settings.*.translations.ar.name.string"                     =>  trans('name should be a string'), 
			 "category_settings.*.category_settings.*.translations.ar.name.required"                   =>  trans('name is required'), 
			 "category_settings.*.category_settings.*.category_id.exists"                              =>  trans('category is not Valid'), 
			 "category_settings.*.category_settings.*.category_id.required"                            =>  trans('category is required'), 
			 "category_settings.*.category_settings.*.addon_price.numeric"                             =>  trans('addon price should be a number'), 
			 "category_settings.*.category_settings.*.addon_price.required"                            =>  trans('addon price is required'), 
			 "category_settings.*.category_settings.*.parent_id.exists"                                =>  trans('parent is not Valid'), 
			 "category_settings.*.category_settings.*.parent_id.required"                              =>  trans('parent is required'), 
			 "category_settings.*.category_settings.*.status.in"                                       =>  trans('status is not allowed'), 
			 "category_settings.*.category_settings.*.status.required"                                 =>  trans('status is required'), 
			 "category_settings.*.category_settings.required"                                          =>  trans('category settings is required'), 
			 "category_settings.required"                                                              =>  trans('category settings is required'), 
			 "date_times.array"                                                                        =>  trans('date times is not array'), 
			 "date_times.*.category_id.exists"                                                         =>  trans('category is not Valid'), 
			 "date_times.*.category_id.required"                                                       =>  trans('category is required'), 
			 "date_times.*.date.date"                                                                  =>  trans('date should be a date'), 
			 "date_times.*.date.required"                                                              =>  trans('date is required'), 
			 "date_times.*.from.time"                                                                  =>  trans('from should be a time'), 
			 "date_times.*.from.required"                                                              =>  trans('from is required'), 
			 "date_times.*.to.time"                                                                    =>  trans('to should be a time'), 
			 "date_times.*.to.required"                                                                =>  trans('to is required'), 
			 "date_times.*.order_count.numeric"                                                        =>  trans('order count should be a number'), 
			 "date_times.*.order_count.required"                                                       =>  trans('order count is required'), 
			 "date_times.required"                                                                     =>  trans('date times is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
