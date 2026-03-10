<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategoryDateTimesRequest extends FormRequest
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
			 "data.*.type"         =>  ['nullable','in:clothes,services,sales,maid,host'], 
			 "data.*.category_id"  =>  ['required','exists:categories,id'], 
			 "data.*.city_id"      =>  ['nullable','exists:cities,id'], 
			 "data.*.date"         =>  ['required','date'], 
			 "data.*.from"         =>  ['required','time'], 
			 "data.*.to"           =>  ['required','time'], 
			 "data.*.order_count"  =>  ['nullable','numeric'], 
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
			 "data.*.type.in"               =>  trans('type is not allowed'), 
			 "data.*.type.required"         =>  trans('type is required'), 
			 "data.*.category_id.exists"    =>  trans('category is not Valid'), 
			 "data.*.category_id.required"  =>  trans('category is required'), 
			 "data.*.city_id.exists"        =>  trans('city is not Valid'), 
			 "data.*.city_id.required"      =>  trans('city is required'), 
			 "data.*.date.date"             =>  trans('date should be a date'), 
			 "data.*.date.required"         =>  trans('date is required'), 
			 "data.*.from.time"             =>  trans('from should be a time'), 
			 "data.*.from.required"         =>  trans('from is required'), 
			 "data.*.to.time"               =>  trans('to should be a time'), 
			 "data.*.to.required"           =>  trans('to is required'), 
			 "data.*.order_count.numeric"   =>  trans('order count should be a number'), 
			 "data.*.order_count.required"  =>  trans('order count is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
