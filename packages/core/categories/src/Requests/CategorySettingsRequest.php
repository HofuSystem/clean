<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategorySettingsRequest extends FormRequest
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
			 "translations.en.name"                               	=>  ['required','string'], 
			 "translations.ar.name"                               	=>  ['required','string'], 
			 "category_id"                                        	=>  ['required','exists:categories,id'], 
			 "addon_price"                                        	=>  ['nullable','numeric'], 
			 "cost"                                        		  	=>  ['nullable','numeric'], 
			 "addon_prices"                                       	=>  ['nullable','array'], 
			 "addon_prices.*.priceable_type"                      	=>  ['required','string'], 
			 "addon_prices.*.city_id"                             	=>  ['required','exists:cities,id'], 
			 "addon_prices.*.price"                               	=>  ['required','numeric'], 
			 "parent_id"                                          	=>  ['nullable','exists:category_settings,id'], 
			 "status"                                             	=>  ['required','in:active,not-active'], 
			 "category_settings"                                  	=>  ['nullable','array'], 
			 "category_settings.*.translations.en.name"           	=>  ['required','string'], 
			 "category_settings.*.translations.ar.name"           	=>  ['required','string'], 
			 "category_settings.*.category_id"                    	=>  ['required','exists:categories,id'], 
			 "category_settings.*.cost"                    			=>  ['nullable','numeric'], 
			 "category_settings.*.addon_price"                    	=>  ['nullable','numeric'], 
			 "category_settings.*.addon_prices"                   	=>  ['nullable','array'], 
			 "category_settings.*.addon_prices.*.priceable_type"  	=>  ['required','string'], 
			 "category_settings.*.addon_prices.*.city_id"         	=>  ['required','exists:cities,id'], 
			 "category_settings.*.addon_prices.*.price"           	=>  ['required','numeric'], 
			 "category_settings.*.addon_prices.*.cost"           	=>  ['required','numeric'], 
			 "category_settings.*.status"                         	=>  ['required','in:active,not-active'], 
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
			 "slug.string"                                                 =>  trans('slug should be a string'), 
			 "slug.required"                                               =>  trans('slug is required'), 
			 "slug.unique"                                                 =>  trans('slug should be unique'), 
			 "translations.en.name.string"                                 =>  trans('name should be a string'), 
			 "translations.en.name.required"                               =>  trans('name is required'), 
			 "translations.ar.name.string"                                 =>  trans('name should be a string'), 
			 "translations.ar.name.required"                               =>  trans('name is required'), 
			 "category_id.exists"                                          =>  trans('category is not Valid'), 
			 "category_id.required"                                        =>  trans('category is required'), 
			 "addon_price.numeric"                                         =>  trans('addon price should be a number'), 
			 "addon_price.required"                                        =>  trans('addon price is required'), 
			 "addon_prices.array"                                          =>  trans('addon prices is not array'), 
			 "addon_prices.*.priceable_type.string"                        =>  trans('priceable should be a string'), 
			 "addon_prices.*.priceable_type.required"                      =>  trans('priceable is required'), 
			 "addon_prices.*.priceable_id.numeric"                         =>  trans('priceable id should be a number'), 
			 "addon_prices.*.priceable_id.required"                        =>  trans('priceable id is required'), 
			 "addon_prices.*.city_id.exists"                               =>  trans('city is not Valid'), 
			 "addon_prices.*.city_id.required"                             =>  trans('city is required'), 
			 "addon_prices.*.price.numeric"                                =>  trans('price should be a number'), 
			 "addon_prices.*.price.required"                               =>  trans('price is required'), 
			 "addon_prices.required"                                       =>  trans('addon prices is required'), 
			 "parent_id.exists"                                            =>  trans('parent is not Valid'), 
			 "parent_id.required"                                          =>  trans('parent is required'), 
			 "status.in"                                                   =>  trans('status is not allowed'), 
			 "status.required"                                             =>  trans('status is required'), 
			 "category_settings.array"                                     =>  trans('category settings is not array'), 
			 "category_settings.*.slug.string"                             =>  trans('slug should be a string'), 
			 "category_settings.*.slug.required"                           =>  trans('slug is required'), 
			 "category_settings.*.slug.unique"                             =>  trans('slug should be unique'), 
			 "category_settings.*.translations.en.name.string"             =>  trans('name should be a string'), 
			 "category_settings.*.translations.en.name.required"           =>  trans('name is required'), 
			 "category_settings.*.translations.ar.name.string"             =>  trans('name should be a string'), 
			 "category_settings.*.translations.ar.name.required"           =>  trans('name is required'), 
			 "category_settings.*.category_id.exists"                      =>  trans('category is not Valid'), 
			 "category_settings.*.category_id.required"                    =>  trans('category is required'), 
			 "category_settings.*.addon_price.numeric"                     =>  trans('addon price should be a number'), 
			 "category_settings.*.addon_price.required"                    =>  trans('addon price is required'), 
			 "category_settings.*.addon_prices.array"                      =>  trans('addon prices is not array'), 
			 "category_settings.*.addon_prices.*.priceable_type.string"    =>  trans('priceable should be a string'), 
			 "category_settings.*.addon_prices.*.priceable_type.required"  =>  trans('priceable is required'), 
			 "category_settings.*.addon_prices.*.priceable_id.numeric"     =>  trans('priceable id should be a number'), 
			 "category_settings.*.addon_prices.*.priceable_id.required"    =>  trans('priceable id is required'), 
			 "category_settings.*.addon_prices.*.city_id.exists"           =>  trans('city is not Valid'), 
			 "category_settings.*.addon_prices.*.city_id.required"         =>  trans('city is required'), 
			 "category_settings.*.addon_prices.*.price.numeric"            =>  trans('price should be a number'), 
			 "category_settings.*.addon_prices.*.price.required"           =>  trans('price is required'), 
			 "category_settings.*.addon_prices.required"                   =>  trans('addon prices is required'), 
			 "category_settings.*.parent_id.exists"                        =>  trans('parent is not Valid'), 
			 "category_settings.*.parent_id.required"                      =>  trans('parent is required'), 
			 "category_settings.*.status.in"                               =>  trans('status is not allowed'), 
			 "category_settings.*.status.required"                         =>  trans('status is required'), 
			 "category_settings.required"                                  =>  trans('category settings is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
