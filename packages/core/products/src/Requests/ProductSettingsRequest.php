<?php

namespace Core\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ProductSettingsRequest extends FormRequest
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
             "translations.en.description"                        	=>  ['nullable','string'], 
             "translations.ar.description"                        	=>  ['nullable','string'], 
             "addon_price"                                        	=>  ['nullable','numeric'], 
             "discount_percent"                               	    =>  ['nullable','numeric'], 
             "cost"                                        		  	=>  ['nullable','numeric'], 
             "addon_prices"                                       	=>  ['nullable','array'], 
             "addon_prices.*.priceable_type"                      	=>  ['required','string'], 
             "addon_prices.*.city_id"                             	=>  ['required','exists:cities,id'], 
             "addon_prices.*.price"                               	=>  ['required','numeric'], 
             "parent_id"                                          	=>  ['nullable','exists:product_settings,id'], 
             "status"                                             	=>  ['required','in:active,not-active'], 
             "color"                                             	=>  ['nullable','string','max:255'], 
             "icon"                                             	=>  ['nullable','string','max:255'], 
             "general"                                             =>  ['nullable','boolean'],
             "product_settings"                                  	=>  ['nullable','array'], 
             "product_settings.*.translations.en.name"           	=>  ['required','string'], 
             "product_settings.*.translations.ar.name"           	=>  ['required','string'], 
             "product_settings.*.translations.en.description"    	=>  ['nullable','string'], 
             "product_settings.*.translations.ar.description"    	=>  ['nullable','string'], 
             "product_settings.*.cost"                    			=>  ['nullable','numeric'], 
             "product_settings.*.discount_percent"               	=>  ['nullable','numeric'], 
             "product_settings.*.addon_price"                    	=>  ['nullable','numeric'], 
             "product_settings.*.addon_prices"                   	=>  ['nullable','array'], 
             "product_settings.*.addon_prices.*.priceable_type"  	=>  ['required','string'], 
             "product_settings.*.addon_prices.*.city_id"         	=>  ['required','exists:cities,id'], 
             "product_settings.*.addon_prices.*.price"           	=>  ['required','numeric'], 
             "product_settings.*.addon_prices.*.cost"           	=>  ['required','numeric'], 
             "product_settings.*.status"                         	=>  ['required','in:active,not-active'], 
             "product_settings.*.color"                         	=>  ['nullable','string','max:255'], 
             "product_settings.*.icon"                         	=>  ['nullable','string','max:255'], 
             "product_settings.*.general"                         	=>  ['nullable','boolean'], 
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
             "product_settings.array"                                     =>  trans('product settings is not array'), 
             "product_settings.*.slug.string"                             =>  trans('slug should be a string'), 
             "product_settings.*.slug.required"                           =>  trans('slug is required'), 
             "product_settings.*.slug.unique"                             =>  trans('slug should be unique'), 
             "product_settings.*.translations.en.name.string"             =>  trans('name should be a string'), 
             "product_settings.*.translations.en.name.required"           =>  trans('name is required'), 
             "product_settings.*.translations.ar.name.string"             =>  trans('name should be a string'), 
             "product_settings.*.translations.ar.name.required"           =>  trans('name is required'), 
             "product_settings.*.addon_price.numeric"                     =>  trans('addon price should be a number'), 
             "product_settings.*.addon_price.required"                    =>  trans('addon price is required'), 
             "product_settings.*.addon_prices.array"                      =>  trans('addon prices is not array'), 
             "product_settings.*.addon_prices.*.priceable_type.string"    =>  trans('priceable should be a string'), 
             "product_settings.*.addon_prices.*.priceable_type.required"  =>  trans('priceable is required'), 
             "product_settings.*.addon_prices.*.priceable_id.numeric"     =>  trans('priceable id should be a number'), 
             "product_settings.*.addon_prices.*.priceable_id.required"    =>  trans('priceable id is required'), 
             "product_settings.*.addon_prices.*.city_id.exists"           =>  trans('city is not Valid'), 
             "product_settings.*.addon_prices.*.city_id.required"         =>  trans('city is required'), 
             "product_settings.*.addon_prices.*.price.numeric"            =>  trans('price should be a number'), 
             "product_settings.*.addon_prices.*.price.required"           =>  trans('price is required'), 
             "product_settings.*.addon_prices.required"                   =>  trans('addon prices is required'), 
             "product_settings.*.parent_id.exists"                        =>  trans('parent is not Valid'), 
             "product_settings.*.parent_id.required"                      =>  trans('parent is required'), 
             "product_settings.*.status.in"                               =>  trans('status is not allowed'), 
             "product_settings.*.status.required"                         =>  trans('status is required'), 
             "product_settings.required"                                  =>  trans('product settings is required'), 
            ]; 
    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }
}
