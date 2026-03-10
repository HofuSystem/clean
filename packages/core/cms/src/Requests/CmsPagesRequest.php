<?php

namespace Core\CMS\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CmsPagesRequest extends FormRequest
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
			 "slug"                            =>  ['required','unique:cms_pages,slug','string'], 
			 "translations.en.name"            =>  ['required','string'], 
			 "translations.ar.name"            =>  ['required','string'], 
			 "is_parent"                       =>  ['nullable','boolean'], 
			 "is_multi_upload"                 =>  ['nullable','boolean'], 
			 "have_point"                      =>  ['nullable','boolean'], 
			 "have_name"                       =>  ['nullable','boolean'], 
			 "have_description"                =>  ['nullable','boolean'], 
			 "have_intro"                      =>  ['nullable','boolean'], 
			 "have_image"                      =>  ['nullable','boolean'], 
			 "have_tablet_image"               =>  ['nullable','boolean'], 
			 "have_mobile_image"               =>  ['nullable','boolean'], 
			 "have_icon"                       =>  ['nullable','boolean'], 
			 "have_video"                      =>  ['nullable','boolean'], 
			 "have_link"                       =>  ['nullable','boolean'], 
			 "details"                         =>  ['nullable','array'], 
			 "details.*.translations.en.name"  =>  ['nullable','string'], 
			 "details.*.translations.ar.name"  =>  ['nullable','string'], 
			 "details.*.video"                 =>  ['nullable','string'], 
			 "details.*.link"                  =>  ['nullable','string'], 
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
			 "slug.string"                              =>  trans('slug should be a string'), 
			 "slug.required"                            =>  trans('slug is required'), 
			 "slug.unique"                              =>  trans('slug should be unique'), 
			 "translations.en.name.string"              =>  trans('name should be a string'), 
			 "translations.en.name.required"            =>  trans('name is required'), 
			 "translations.ar.name.string"              =>  trans('name should be a string'), 
			 "translations.ar.name.required"            =>  trans('name is required'), 
			 "is_parent.boolean"                        =>  trans('is parent should be a boolean'), 
			 "is_parent.required"                       =>  trans('is parent is required'), 
			 "is_multi_upload.boolean"                  =>  trans('is multi upload should be a boolean'), 
			 "is_multi_upload.required"                 =>  trans('is multi upload is required'), 
			 "have_point.boolean"                       =>  trans('have point should be a boolean'), 
			 "have_point.required"                      =>  trans('have point is required'), 
			 "have_name.boolean"                        =>  trans('have name should be a boolean'), 
			 "have_name.required"                       =>  trans('have name is required'), 
			 "have_description.boolean"                 =>  trans('have description should be a boolean'), 
			 "have_description.required"                =>  trans('have description is required'), 
			 "have_intro.boolean"                       =>  trans('have intro should be a boolean'), 
			 "have_intro.required"                      =>  trans('have intro is required'), 
			 "have_image.boolean"                       =>  trans('have image should be a boolean'), 
			 "have_image.required"                      =>  trans('have image is required'), 
			 "have_tablet_image.boolean"                =>  trans('have tablet image should be a boolean'), 
			 "have_tablet_image.required"               =>  trans('have tablet image is required'), 
			 "have_mobile_image.boolean"                =>  trans('have mobile image should be a boolean'), 
			 "have_mobile_image.required"               =>  trans('have mobile image is required'), 
			 "have_icon.boolean"                        =>  trans('have icon should be a boolean'), 
			 "have_icon.required"                       =>  trans('have icon is required'), 
			 "have_video.boolean"                       =>  trans('have video should be a boolean'), 
			 "have_video.required"                      =>  trans('have video is required'), 
			 "have_link.boolean"                        =>  trans('have link should be a boolean'), 
			 "have_link.required"                       =>  trans('have link is required'), 
			 "details.array"                            =>  trans('details is not array'), 
			 "details.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "details.*.translations.en.name.required"  =>  trans('name is required'), 
			 "details.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "details.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "details.*.video.string"                   =>  trans('video should be a string'), 
			 "details.*.video.required"                 =>  trans('video is required'), 
			 "details.*.link.string"                    =>  trans('link should be a string'), 
			 "details.*.link.required"                  =>  trans('link is required'), 
			 "details.*.cms_pages_id.exists"            =>  trans('cms pages is not Valid'), 
			 "details.*.cms_pages_id.required"          =>  trans('cms pages is required'), 
			 "details.required"                         =>  trans('details is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
