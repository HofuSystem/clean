<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategoryTimesRequest extends FormRequest
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
			 "data.*.category_id"  =>  ['required','exists:categories,id'], 
			 "data.*.day"          =>  ['required','in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'], 
			 "data.*.from"         =>  ['required','time'], 
			 "data.*.to"           =>  ['required','time'], 
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
			 "data.*.day.in"                =>  trans('day is not allowed'), 
			 "data.*.day.required"          =>  trans('day is required'), 
			 "data.*.from.time"             =>  trans('from should be a time'), 
			 "data.*.from.required"         =>  trans('from is required'), 
			 "data.*.to.time"               =>  trans('to should be a time'), 
			 "data.*.to.required"           =>  trans('to is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
