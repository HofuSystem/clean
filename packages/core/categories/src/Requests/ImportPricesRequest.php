<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportPricesRequest extends FormRequest
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
			 "data.*.priceable_type"  =>  ['required','string'], 
			 "data.*.priceable_id"    =>  ['required','numeric'], 
			 "data.*.city_id"         =>  ['required','exists:cities,id'], 
			 "data.*.price"           =>  ['required','numeric'], 
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
			 "data.*.priceable_type.string"    =>  trans('priceable should be a string'), 
			 "data.*.priceable_type.required"  =>  trans('priceable is required'), 
			 "data.*.priceable_id.numeric"     =>  trans('priceable id should be a number'), 
			 "data.*.priceable_id.required"    =>  trans('priceable id is required'), 
			 "data.*.city_id.exists"           =>  trans('city is not Valid'), 
			 "data.*.city_id.required"         =>  trans('city is required'), 
			 "data.*.price.numeric"            =>  trans('price should be a number'), 
			 "data.*.price.required"           =>  trans('price is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
