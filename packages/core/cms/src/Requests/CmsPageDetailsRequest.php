<?php

namespace Core\CMS\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CmsPageDetailsRequest extends FormRequest
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
			 "translations.en.name"  =>  ['nullable','string'], 
			 "translations.ar.name"  =>  ['nullable','string'], 
			 "video"                 =>  ['nullable','string'], 
			 "link"                  =>  ['nullable','string'], 
			 "cms_pages_id"          =>  ['required','exists:cms_pages,id'], 
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
			 "video.string"                   =>  trans('video should be a string'), 
			 "video.required"                 =>  trans('video is required'), 
			 "link.string"                    =>  trans('link should be a string'), 
			 "link.required"                  =>  trans('link is required'), 
			 "cms_pages_id.exists"            =>  trans('cms pages is not Valid'), 
			 "cms_pages_id.required"          =>  trans('cms pages is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
