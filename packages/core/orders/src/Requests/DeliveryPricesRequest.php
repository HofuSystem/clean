<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class DeliveryPricesRequest extends FormRequest
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
			 "category_id"  =>  ['nullable','exists:categories,id'], 
			 "city_id"      =>  ['nullable','exists:cities,id'], 
			 "district_id"  =>  ['nullable','exists:districts,id'], 
			 "price"        =>  ['required','numeric'], 
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
			 "category_id.exists"    =>  trans('category is not Valid'), 
			 "category_id.required"  =>  trans('category is required'), 
			 "city.exists"           =>  trans('city is not Valid'), 
			 "city.required"         =>  trans('city is required'), 
			 "district_id.exists"    =>  trans('district is not Valid'), 
			 "district_id.required"  =>  trans('district is required'), 
			 "price.numeric"         =>  trans('price should be a number'), 
			 "price.required"        =>  trans('price is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
