<?php

namespace Core\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportProductsRequest extends FormRequest
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
			 "data.*.translations.en.name"     =>  ['required','string'], 
			 "data.*.translations.ar.name"     =>  ['required','string'], 
			 "data.*.type"                     =>  ['required','in:clothes,sales,services'], 
			 "data.*.sku"                      =>  ['required','unique:products,sku','string'], 
			 "data.*.is_package"               =>  ['nullable','boolean'], 
			 "data.*.category_id"              =>  ['required','exists:categories,id'], 
			 "data.*.sub_category_id"          =>  ['nullable','exists:categories,id'], 
			 "data.*.price"                    =>  ['required','numeric'], 
			 "data.*.prices"                   =>  ['nullable','array'], 
			 "data.*.prices.*.priceable_type"  =>  ['required','string'], 
			 "data.*.prices.*.city_id"         =>  ['required','exists:cities,id'], 
			 "data.*.prices.*.price"           =>  ['required','numeric'], 
			 "data.*.quantity"                 =>  ['required','numeric'], 
			 "data.*.status"                   =>  ['required','in:active,not-active'], 
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
			 "data.*.translations.en.name.string"       =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"     =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"       =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"     =>  trans('name is required'), 
			 "data.*.type.in"                           =>  trans('type is not allowed'), 
			 "data.*.type.required"                     =>  trans('type is required'), 
			 "data.*.sku.string"                        =>  trans('sku should be a string'), 
			 "data.*.sku.required"                      =>  trans('sku is required'), 
			 "data.*.sku.unique"                        =>  trans('sku should be unique'), 
			 "data.*.is_package.boolean"                =>  trans('is package should be a boolean'), 
			 "data.*.is_package.required"               =>  trans('is package is required'), 
			 "data.*.category_id.exists"                =>  trans('category is not Valid'), 
			 "data.*.category_id.required"              =>  trans('category is required'), 
			 "data.*.sub_category_id.exists"            =>  trans('sub category is not Valid'), 
			 "data.*.sub_category_id.required"          =>  trans('sub category is required'), 
			 "data.*.price.numeric"                     =>  trans('price should be a number'), 
			 "data.*.price.required"                    =>  trans('price is required'), 
			 "data.*.prices.array"                      =>  trans('prices is not array'), 
			 "data.*.prices.*.priceable_type.string"    =>  trans('priceable should be a string'), 
			 "data.*.prices.*.priceable_type.required"  =>  trans('priceable is required'), 
			 "data.*.prices.*.priceable_id.numeric"     =>  trans('priceable id should be a number'), 
			 "data.*.prices.*.priceable_id.required"    =>  trans('priceable id is required'), 
			 "data.*.prices.*.city_id.exists"           =>  trans('city is not Valid'), 
			 "data.*.prices.*.city_id.required"         =>  trans('city is required'), 
			 "data.*.prices.*.price.numeric"            =>  trans('price should be a number'), 
			 "data.*.prices.*.price.required"           =>  trans('price is required'), 
			 "data.*.prices.required"                   =>  trans('prices is required'), 
			 "data.*.quantity.numeric"                  =>  trans('quantity should be a number'), 
			 "data.*.quantity.required"                 =>  trans('quantity is required'), 
			 "data.*.status.in"                         =>  trans('status is not allowed'), 
			 "data.*.status.required"                   =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
