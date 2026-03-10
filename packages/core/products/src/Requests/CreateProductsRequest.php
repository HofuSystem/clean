<?php

namespace Core\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CreateProductsRequest extends FormRequest
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
			 "translations.en.name"  		=>  ['required','string'], 
			 "translations.ar.name"  		=>  ['required','string'], 
			 "type"                  		=>  ['required','in:clothes,sales,services'], 
			 "is_package"            		=>  ['nullable','boolean'], 
			 "category_id"           		=>  ['required','exists:categories,id'], 
			 "version"    					=>  ['nullable','required_if:type,clothes','array'], 
			 "version.*.sub_category_id"    =>  ['nullable','exists:categories,id'], 
			 "version.*.sku"                =>  ['required','unique:products,sku','string'], 
			 "version.*.price"              =>  ['required','numeric'], 
			 "quantity"             		=>  ['nullable','required_if:type,sales','numeric'], 
			 "price"             			=>  ['nullable','required_if:type,sales','required_if:type,services','numeric'], 
			 "status"                		=>  ['required','in:active,not-active'], 
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
            'translations.en.name.required'       => __('Product name in English is required.'),
            'translations.en.name.string'         => __('Product name in English must be text.'),
            'translations.ar.name.required'       => __('Product name in Arabic is required.'),
            'translations.ar.name.string'         => __('Product name in Arabic must be text.'),
            'type.required'                       => __('Product type is required.'),
            'type.in'                             => __('Product type must be one of: clothes, sales, or services.'),
            'is_package.boolean'                  => __('"Is package" must be yes or no.'),
            'category_id.required'                => __('Please select a category for the product.'),
            'category_id.exists'                  => __('The selected category is invalid or does not exist.'),
            'version.required_if'                 => __('When product type is clothes, at least one version (SKU and price) is required.'),
            'version.array'                       => __('Version data must be a list.'),
            'version.*.sub_category_id.exists'    => __('The selected sub-category is invalid or does not exist.'),
            'version.*.sku.required'              => __('SKU is required for each version.'),
            'version.*.sku.string'                => __('SKU for each version must be text.'),
            'version.*.sku.unique'                => __('This SKU is already used. Please choose a unique code for each version.'),
            'version.*.price.required'             => __('Price is required for each version.'),
            'version.*.price.numeric'             => __('Price for each version must be a valid number.'),
            'quantity.required_if'                => __('Quantity is required when product type is sales.'),
            'quantity.numeric'                    => __('Quantity must be a number.'),
            'price.required_if'                    => __('Price is required when product type is sales or services.'),
            'price.numeric'                       => __('Price must be a valid number.'),
            'status.required'                      => __('Product status (active / not active) is required.'),
            'status.in'                           => __('Status must be either active or not-active.'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
