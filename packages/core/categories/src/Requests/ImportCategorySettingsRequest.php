<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategorySettingsRequest extends FormRequest
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
			 "data.*.translations.en.name"                               =>  ['required','string'], 
			 "data.*.translations.ar.name"                               =>  ['required','string'], 
			 "data.*.category_id"                                        =>  ['required','exists:categories,id'], 
			 "data.*.addon_price"                                        =>  ['nullable','numeric'], 
			 "data.*.addon_prices"                                       =>  ['nullable','array'], 
			 "data.*.addon_prices.*.priceable_type"                      =>  ['required','string'], 
			 "data.*.addon_prices.*.city_id"                             =>  ['required','exists:cities,id'], 
			 "data.*.addon_prices.*.price"                               =>  ['required','numeric'], 
			 "data.*.parent_id"                                          =>  ['nullable','exists:category_settings,id'], 
			 "data.*.status"                                             =>  ['required','in:active,not-active'], 
			 "data.*.category_settings"                                  =>  ['nullable','array'], 
			 "data.*.category_settings.*.translations.en.name"           =>  ['required','string'], 
			 "data.*.category_settings.*.translations.ar.name"           =>  ['required','string'], 
			 "data.*.category_settings.*.category_id"                    =>  ['required','exists:categories,id'], 
			 "data.*.category_settings.*.addon_price"                    =>  ['nullable','numeric'], 
			 "data.*.category_settings.*.addon_prices"                   =>  ['nullable','array'], 
			 "data.*.category_settings.*.addon_prices.*.priceable_type"  =>  ['required','string'], 
			 "data.*.category_settings.*.addon_prices.*.city_id"         =>  ['required','exists:cities,id'], 
			 "data.*.category_settings.*.addon_prices.*.price"           =>  ['required','numeric'], 
			 "data.*.category_settings.*.status"                         =>  ['required','in:active,not-active'], 
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
			 "data.*.slug.string"                                                 =>  trans('slug should be a string'), 
			 "data.*.slug.required"                                               =>  trans('slug is required'), 
			 "data.*.slug.unique"                                                 =>  trans('slug should be unique'), 
			 "data.*.translations.en.name.string"                                 =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"                               =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"                                 =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"                               =>  trans('name is required'), 
			 "data.*.category_id.exists"                                          =>  trans('category is not Valid'), 
			 "data.*.category_id.required"                                        =>  trans('category is required'), 
			 "data.*.addon_price.numeric"                                         =>  trans('addon price should be a number'), 
			 "data.*.addon_price.required"                                        =>  trans('addon price is required'), 
			 "data.*.addon_prices.array"                                          =>  trans('addon prices is not array'), 
			 "data.*.addon_prices.*.priceable_type.string"                        =>  trans('priceable should be a string'), 
			 "data.*.addon_prices.*.priceable_type.required"                      =>  trans('priceable is required'), 
			 "data.*.addon_prices.*.priceable_id.numeric"                         =>  trans('priceable id should be a number'), 
			 "data.*.addon_prices.*.priceable_id.required"                        =>  trans('priceable id is required'), 
			 "data.*.addon_prices.*.city_id.exists"                               =>  trans('city is not Valid'), 
			 "data.*.addon_prices.*.city_id.required"                             =>  trans('city is required'), 
			 "data.*.addon_prices.*.price.numeric"                                =>  trans('price should be a number'), 
			 "data.*.addon_prices.*.price.required"                               =>  trans('price is required'), 
			 "data.*.addon_prices.required"                                       =>  trans('addon prices is required'), 
			 "data.*.parent_id.exists"                                            =>  trans('parent is not Valid'), 
			 "data.*.parent_id.required"                                          =>  trans('parent is required'), 
			 "data.*.status.in"                                                   =>  trans('status is not allowed'), 
			 "data.*.status.required"                                             =>  trans('status is required'), 
			 "data.*.category_settings.array"                                     =>  trans('category settings is not array'), 
			 "data.*.category_settings.*.slug.string"                             =>  trans('slug should be a string'), 
			 "data.*.category_settings.*.slug.required"                           =>  trans('slug is required'), 
			 "data.*.category_settings.*.slug.unique"                             =>  trans('slug should be unique'), 
			 "data.*.category_settings.*.translations.en.name.string"             =>  trans('name should be a string'), 
			 "data.*.category_settings.*.translations.en.name.required"           =>  trans('name is required'), 
			 "data.*.category_settings.*.translations.ar.name.string"             =>  trans('name should be a string'), 
			 "data.*.category_settings.*.translations.ar.name.required"           =>  trans('name is required'), 
			 "data.*.category_settings.*.category_id.exists"                      =>  trans('category is not Valid'), 
			 "data.*.category_settings.*.category_id.required"                    =>  trans('category is required'), 
			 "data.*.category_settings.*.addon_price.numeric"                     =>  trans('addon price should be a number'), 
			 "data.*.category_settings.*.addon_price.required"                    =>  trans('addon price is required'), 
			 "data.*.category_settings.*.addon_prices.array"                      =>  trans('addon prices is not array'), 
			 "data.*.category_settings.*.addon_prices.*.priceable_type.string"    =>  trans('priceable should be a string'), 
			 "data.*.category_settings.*.addon_prices.*.priceable_type.required"  =>  trans('priceable is required'), 
			 "data.*.category_settings.*.addon_prices.*.priceable_id.numeric"     =>  trans('priceable id should be a number'), 
			 "data.*.category_settings.*.addon_prices.*.priceable_id.required"    =>  trans('priceable id is required'), 
			 "data.*.category_settings.*.addon_prices.*.city_id.exists"           =>  trans('city is not Valid'), 
			 "data.*.category_settings.*.addon_prices.*.city_id.required"         =>  trans('city is required'), 
			 "data.*.category_settings.*.addon_prices.*.price.numeric"            =>  trans('price should be a number'), 
			 "data.*.category_settings.*.addon_prices.*.price.required"           =>  trans('price is required'), 
			 "data.*.category_settings.*.addon_prices.required"                   =>  trans('addon prices is required'), 
			 "data.*.category_settings.*.parent_id.exists"                        =>  trans('parent is not Valid'), 
			 "data.*.category_settings.*.parent_id.required"                      =>  trans('parent is required'), 
			 "data.*.category_settings.*.status.in"                               =>  trans('status is not allowed'), 
			 "data.*.category_settings.*.status.required"                         =>  trans('status is required'), 
			 "data.*.category_settings.required"                                  =>  trans('category settings is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
