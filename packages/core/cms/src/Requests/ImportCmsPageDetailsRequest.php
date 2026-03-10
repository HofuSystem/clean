<?php

namespace Core\CMS\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCmsPageDetailsRequest extends FormRequest
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
			 "data.*.translations.en.name"  =>  ['nullable','string'], 
			 "data.*.translations.ar.name"  =>  ['nullable','string'], 
			 "data.*.video"                 =>  ['nullable','string'], 
			 "data.*.link"                  =>  ['nullable','string'], 
			 "data.*.cms_pages_id"          =>  ['required','exists:cms_pages,id'], 
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
			 "data.*.video.string"                   =>  trans('video should be a string'), 
			 "data.*.video.required"                 =>  trans('video is required'), 
			 "data.*.link.string"                    =>  trans('link should be a string'), 
			 "data.*.link.required"                  =>  trans('link is required'), 
			 "data.*.cms_pages_id.exists"            =>  trans('cms pages is not Valid'), 
			 "data.*.cms_pages_id.required"          =>  trans('cms pages is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
