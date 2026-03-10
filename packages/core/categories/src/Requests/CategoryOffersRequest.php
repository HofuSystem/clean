<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryOffersRequest extends FormRequest
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
			 "translations.en.name"  =>  ['required','string'], 
			 "translations.ar.name"  =>  ['required','string'], 
			 "price"                 =>  ['required','numeric'], 
			 "sale_price"            =>  ['required','numeric'], 
			 "hours_num"             =>  ['required','numeric'], 
			 "workers_num"           =>  ['required','numeric'], 
			 "status"                =>  ['required','in:active,not-active'], 
			 "type"                  =>  ['required','in:services,clothes,sales,maid,host'], 
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
			 "translations.en.name.string"    =>  trans('name should be a string'), 
			 "translations.en.name.required"  =>  trans('name is required'), 
			 "translations.ar.name.string"    =>  trans('name should be a string'), 
			 "translations.ar.name.required"  =>  trans('name is required'), 
			 "price.numeric"                  =>  trans('price should be a number'), 
			 "price.required"                 =>  trans('price is required'), 
			 "sale_price.numeric"             =>  trans('sale price should be a number'), 
			 "sale_price.required"            =>  trans('sale price is required'), 
			 "hours_num.numeric"              =>  trans('hours num should be a number'), 
			 "hours_num.required"             =>  trans('hours num is required'), 
			 "workers_num.numeric"            =>  trans('workers num should be a number'), 
			 "workers_num.required"           =>  trans('workers num is required'), 
			 "status.in"                      =>  trans('status is not allowed'), 
			 "status.required"                =>  trans('status is required'), 
			 "type.in"                        =>  trans('type is not allowed'), 
			 "type.required"                  =>  trans('type is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
