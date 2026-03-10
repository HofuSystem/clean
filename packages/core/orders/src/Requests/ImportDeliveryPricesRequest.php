<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportDeliveryPricesRequest extends FormRequest
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
			 "data.*.category_id"  =>  ['nullable','exists:categories,id'], 
			 "data.*.city"         =>  ['nullable','exists:cities,'], 
			 "data.*.district_id"  =>  ['nullable','exists:districts,id'], 
			 "data.*.price"        =>  ['required','numeric'], 
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
			 "data.*.category_id.exists"    =>  trans('category is not Valid'), 
			 "data.*.category_id.required"  =>  trans('category is required'), 
			 "data.*.city.exists"           =>  trans('city is not Valid'), 
			 "data.*.city.required"         =>  trans('city is required'), 
			 "data.*.district_id.exists"    =>  trans('district is not Valid'), 
			 "data.*.district_id.required"  =>  trans('district is required'), 
			 "data.*.price.numeric"         =>  trans('price should be a number'), 
			 "data.*.price.required"        =>  trans('price is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
