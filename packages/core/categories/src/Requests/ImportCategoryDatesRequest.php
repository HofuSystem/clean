<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCategoryDatesRequest extends FormRequest
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
			 "data.*.type"         =>  ['required','in:clothes,sales,services,maid,host'], 
			 "data.*.category_id"  =>  ['required','exists:categories,id'], 
			 "data.*.city_id"      =>  ['required','exists:cities,id'], 
			 "data.*.date"         =>  ['nullable','date'], 
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
			 "data.*.date.date"             =>  trans('date should be a date'), 
			 "data.*.date.required"         =>  trans('date is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
