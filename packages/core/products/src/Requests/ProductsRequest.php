<?php

namespace Core\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ProductsRequest extends FormRequest
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
			 "translations.en.name"     =>  ['required','string'], 
			 "translations.ar.name"     =>  ['required','string'], 
			 "type"                     =>  ['required','in:clothes,sales,services'], 
			 "sku"                      =>  ['required','unique:products,sku,'.$this->id,'string'], 
			 "is_package"               =>  ['nullable','boolean'], 
			 "category_id"              =>  ['required','exists:categories,id'], 
			 "sub_category_id"          =>  ['nullable','exists:categories,id'], 
			 "price"                    =>  ['required','numeric'], 
			 "prices"                   =>  ['nullable','array'], 
			 "prices.*.priceable_type"  =>  ['required','string'], 
			 "prices.*.city_id"         =>  ['required','exists:cities,id'], 
			 "prices.*.price"           =>  ['required','numeric'], 
			 "quantity"                 =>  ['nullable','numeric'], 
			 "status"                   =>  ['required','in:active,not-active'], 
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
            'translations.en.name.required'     => __('Product name in English is required.'),
            'translations.en.name.string'       => __('Product name in English must be text.'),
            'translations.ar.name.required'     => __('Product name in Arabic is required.'),
            'translations.ar.name.string'       => __('Product name in Arabic must be text.'),
            'type.required'                     => __('Product type is required.'),
            'type.in'                           => __('Product type must be one of: clothes, sales, or services.'),
            'sku.required'                      => __('Product SKU (stock keeping unit) is required.'),
            'sku.string'                        => __('Product SKU must be text.'),
            'sku.unique'                        => __('This SKU is already used by another product. Please choose a unique code.'),
            'is_package.boolean'                => __('"Is package" must be yes or no.'),
            'is_package.required'               => __('Please specify whether this product is a package.'),
            'category_id.required'              => __('Please select a category for the product.'),
            'category_id.exists'                => __('The selected category is invalid or does not exist.'),
            'sub_category_id.required'          => __('Sub-category is required.'),
            'sub_category_id.exists'            => __('The selected sub-category is invalid or does not exist.'),
            'price.required'                    => __('Product price is required.'),
            'price.numeric'                     => __('Product price must be a valid number.'),
            'prices.array'                      => __('Prices must be sent as a list.'),
            'prices.required'                   => __('At least one price entry is required when using city prices.'),
            'prices.*.priceable_type.required'  => __('Price type is required for each price entry.'),
            'prices.*.priceable_type.string'    => __('Price type must be text.'),
            'prices.*.priceable_id.required'     => __('Priceable ID is required for each price entry.'),
            'prices.*.priceable_id.numeric'     => __('Priceable ID must be a number.'),
            'prices.*.city_id.required'         => __('City is required for each price entry.'),
            'prices.*.city_id.exists'           => __('The selected city is invalid or does not exist.'),
            'prices.*.price.required'            => __('Price amount is required for each city.'),
            'prices.*.price.numeric'            => __('Price amount must be a valid number.'),
            'quantity.numeric'                  => __('Quantity must be a number.'),
            'quantity.required'                 => __('Quantity is required.'),
            'status.required'                   => __('Product status (active / not active) is required.'),
            'status.in'                         => __('Status must be either active or not-active.'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
