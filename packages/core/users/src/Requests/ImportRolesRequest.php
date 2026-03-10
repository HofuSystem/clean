<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportRolesRequest extends FormRequest
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
			 "data.*.translations.en.title"  =>  ['required','string'], 
			 "data.*.translations.ar.title"  =>  ['required','string'], 
			 "data.*.name"                   =>  ['required','unique:roles,name','string'], 
			 "data.*.permissions"            =>  ['nullable','array'], 
			 "data.*.permissions.*"          =>  ['exists:permissions,id'], 
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
			 "data.*.translations.en.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.en.title.required"  =>  trans('title is required'), 
			 "data.*.translations.ar.title.string"    =>  trans('title should be a string'), 
			 "data.*.translations.ar.title.required"  =>  trans('title is required'), 
			 "data.*.name.string"                     =>  trans('name should be a string'), 
			 "data.*.name.required"                   =>  trans('name is required'), 
			 "data.*.name.unique"                     =>  trans('name should be unique'), 
			 "data.*.permissions.array"               =>  trans('permissions is not array'), 
			 "data.*.permissions.*.exists.*"          =>  trans('permissions is not Valid'), 
			 "data.*.permissions.required"            =>  trans('permissions is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
