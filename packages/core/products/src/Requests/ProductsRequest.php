<?php

namespace Core\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;
use Core\Categories\Models\Category;
use Illuminate\Validation\Rule;

class ProductsRequest extends FormRequest
{
    use ApiResponse;

    private bool $categoryHasSubcategories = false;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Package products are determined by their parent category. Keep the
        // product flag synchronized because older app versions still consume it.
        $category = $this->category_id
            ? Category::query()->select(['id', 'is_package'])->find($this->category_id)
            : null;
        $isPackage = (bool) $category?->is_package;
        $this->categoryHasSubcategories = (bool) $category?->subCategories()->exists();
        $normalized = [
            'is_package' => $isPackage ? 1 : 0,
        ];

        if ($isPackage && is_array($this->input('variants')) && !empty($this->input('variants'))) {
            $defaultVariant = array_values($this->input('variants'))[0];
            $normalized['price'] = $defaultVariant['price'] ?? null;
            $normalized['points'] = $defaultVariant['points'] ?? ($defaultVariant['price'] ?? null);
            $normalized['cost'] = $defaultVariant['cost'] ?? 0;
            $normalized['quantity'] = null;
        }

        if ($this->type !== 'sales') {
            $normalized['quantity'] = null;
            $normalized['display_as'] = null;
        } elseif (!$this->categoryHasSubcategories) {
            $normalized['sub_category_id'] = null;
        }

        $this->merge($normalized);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $isPackage = !empty($this->is_package);

      return [ 
			 "translations.en.name"     =>  ['required','string'], 
			 "translations.ar.name"     =>  ['required','string'], 
			 "translations.en.desc"     =>  ['nullable','string'], 
			 "translations.ar.desc"     =>  ['nullable','string'], 
			 "type"                     =>  ['required','in:clothes,sales,services'], 
			 "sku"                      =>  ['nullable','unique:products,sku,'.$this->id,'string'], 
			 "is_package"               =>  ['nullable','boolean'], 
			 "category_id"              =>  ['required','exists:categories,id'], 
			 "sub_category_id"          =>  ['nullable', Rule::requiredIf(fn() => $this->categoryHasSubcategories && empty($this->is_package)), Rule::exists('categories', 'id')->where(fn($query) => $query->where('parent_id', $this->category_id))],
			 "price"                    =>  ['nullable', Rule::requiredIf(fn() => !$isPackage), 'numeric'],
			 "points"                   =>  ['nullable', Rule::requiredIf(fn() => !$isPackage), 'numeric'],
			 "cost"                     =>  ['nullable', Rule::requiredIf(fn() => !$isPackage), 'numeric'],
			 "prices"                   =>  ['nullable','array'],
			 "prices_present"           =>  ['nullable','boolean'],
			 "prices.*.id"              =>  ['nullable','integer','exists:prices,id'],
			 "prices.*.city_id"         =>  ['required_with:prices','integer','exists:cities,id','distinct'],
			 "prices.*.price"           =>  ['required_with:prices','numeric','min:0'],
			 "prices.*.cost"            =>  ['required_with:prices','numeric','min:0'],
			 "quantity"                 =>  ['nullable', Rule::requiredIf(fn() => $this->type === 'sales'), 'integer', 'min:0'],
			 "status"                   =>  ['required','in:active,not-active'], 
			 "display_as"               =>  ['nullable',\Illuminate\Validation\Rule::requiredIf(fn() => $this->type === 'sales'),'in:main,addon'],
			 "variants"                 =>  ['nullable', Rule::requiredIf(fn() => !empty($this->is_package)), 'array', 'min:1'],
			 "variants_present"         =>  ['nullable','boolean'],
			 "variants.*.id"            =>  ['nullable','integer','exists:product_variants,id'],
			 "variants.*.pieces_count"  =>  ['required_with:variants','integer','min:1','distinct'],
			 "variants.*.price"         =>  ['required_with:variants','numeric','min:0'],
			 "variants.*.cost"          =>  ['required_with:variants','numeric','min:0'],
			 "variants.*.points"        =>  ['required_with:variants','numeric','min:0'],
			 "variants.*.extra_piece_price" => ['nullable','numeric','min:0'],
			 "variants.*.status"        =>  ['required_with:variants','in:active,not-active'],
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
            'points.required'                   => __('Product points is required.'),
            'points.numeric'                    => __('Product points must be a valid number.'),
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
