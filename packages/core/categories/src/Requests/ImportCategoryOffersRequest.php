<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategoryOffersRequest extends FormRequest
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
			 "data.*.translations.en.name"  =>  ['required','string'], 
			 "data.*.translations.ar.name"  =>  ['required','string'], 
			 "data.*.price"                 =>  ['required','numeric'], 
			 "data.*.sale_price"            =>  ['required','numeric'], 
			 "data.*.hours_num"             =>  ['required','numeric'], 
			 "data.*.workers_num"           =>  ['required','numeric'], 
			 "data.*.status"                =>  ['required','in:active,not-active'], 
			 "data.*.type"                  =>  ['required','in:services,clothes,sales,maid,host'], 
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
			 "data.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.price.numeric"                  =>  trans('price should be a number'), 
			 "data.*.price.required"                 =>  trans('price is required'), 
			 "data.*.sale_price.numeric"             =>  trans('sale price should be a number'), 
			 "data.*.sale_price.required"            =>  trans('sale price is required'), 
			 "data.*.hours_num.numeric"              =>  trans('hours num should be a number'), 
			 "data.*.hours_num.required"             =>  trans('hours num is required'), 
			 "data.*.workers_num.numeric"            =>  trans('workers num should be a number'), 
			 "data.*.workers_num.required"           =>  trans('workers num is required'), 
			 "data.*.status.in"                      =>  trans('status is not allowed'), 
			 "data.*.status.required"                =>  trans('status is required'), 
			 "data.*.type.in"                        =>  trans('type is not allowed'), 
			 "data.*.type.required"                  =>  trans('type is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
