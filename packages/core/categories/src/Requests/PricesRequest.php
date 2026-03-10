<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PricesRequest extends FormRequest
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
			 "priceable_type"  =>  ['required','string'], 
			 "priceable_id"    =>  ['required','numeric'], 
			 "city_id"         =>  ['required','exists:cities,id'], 
			 "price"           =>  ['required','numeric'], 
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
			 "priceable_type.string"    =>  trans('priceable should be a string'), 
			 "priceable_type.required"  =>  trans('priceable is required'), 
			 "priceable_id.numeric"     =>  trans('priceable id should be a number'), 
			 "priceable_id.required"    =>  trans('priceable id is required'), 
			 "city_id.exists"           =>  trans('city is not Valid'), 
			 "city_id.required"         =>  trans('city is required'), 
			 "price.numeric"            =>  trans('price should be a number'), 
			 "price.required"           =>  trans('price is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
