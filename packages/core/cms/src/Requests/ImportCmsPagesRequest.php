<?php

namespace Core\CMS\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCmsPagesRequest extends FormRequest
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
			 "data.*.slug"                            =>  ['required','unique:cms_pages,slug','string'], 
			 "data.*.translations.en.name"            =>  ['required','string'], 
			 "data.*.translations.ar.name"            =>  ['required','string'], 
			 "data.*.is_parent"                       =>  ['nullable','boolean'], 
			 "data.*.is_multi_upload"                 =>  ['nullable','boolean'], 
			 "data.*.have_point"                      =>  ['nullable','boolean'], 
			 "data.*.have_name"                       =>  ['nullable','boolean'], 
			 "data.*.have_description"                =>  ['nullable','boolean'], 
			 "data.*.have_intro"                      =>  ['nullable','boolean'], 
			 "data.*.have_image"                      =>  ['nullable','boolean'], 
			 "data.*.have_tablet_image"               =>  ['nullable','boolean'], 
			 "data.*.have_mobile_image"               =>  ['nullable','boolean'], 
			 "data.*.have_icon"                       =>  ['nullable','boolean'], 
			 "data.*.have_video"                      =>  ['nullable','boolean'], 
			 "data.*.have_link"                       =>  ['nullable','boolean'], 
			 "data.*.details"                         =>  ['nullable','array'], 
			 "data.*.details.*.translations.en.name"  =>  ['nullable','string'], 
			 "data.*.details.*.translations.ar.name"  =>  ['nullable','string'], 
			 "data.*.details.*.video"                 =>  ['nullable','string'], 
			 "data.*.details.*.link"                  =>  ['nullable','string'], 
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
			 "data.*.slug.string"                              =>  trans('slug should be a string'), 
			 "data.*.slug.required"                            =>  trans('slug is required'), 
			 "data.*.slug.unique"                              =>  trans('slug should be unique'), 
			 "data.*.translations.en.name.string"              =>  trans('name should be a string'), 
			 "data.*.translations.en.name.required"            =>  trans('name is required'), 
			 "data.*.translations.ar.name.string"              =>  trans('name should be a string'), 
			 "data.*.translations.ar.name.required"            =>  trans('name is required'), 
			 "data.*.is_parent.boolean"                        =>  trans('is parent should be a boolean'), 
			 "data.*.is_parent.required"                       =>  trans('is parent is required'), 
			 "data.*.is_multi_upload.boolean"                  =>  trans('is multi upload should be a boolean'), 
			 "data.*.is_multi_upload.required"                 =>  trans('is multi upload is required'), 
			 "data.*.have_point.boolean"                       =>  trans('have point should be a boolean'), 
			 "data.*.have_point.required"                      =>  trans('have point is required'), 
			 "data.*.have_name.boolean"                        =>  trans('have name should be a boolean'), 
			 "data.*.have_name.required"                       =>  trans('have name is required'), 
			 "data.*.have_description.boolean"                 =>  trans('have description should be a boolean'), 
			 "data.*.have_description.required"                =>  trans('have description is required'), 
			 "data.*.have_intro.boolean"                       =>  trans('have intro should be a boolean'), 
			 "data.*.have_intro.required"                      =>  trans('have intro is required'), 
			 "data.*.have_image.boolean"                       =>  trans('have image should be a boolean'), 
			 "data.*.have_image.required"                      =>  trans('have image is required'), 
			 "data.*.have_tablet_image.boolean"                =>  trans('have tablet image should be a boolean'), 
			 "data.*.have_tablet_image.required"               =>  trans('have tablet image is required'), 
			 "data.*.have_mobile_image.boolean"                =>  trans('have mobile image should be a boolean'), 
			 "data.*.have_mobile_image.required"               =>  trans('have mobile image is required'), 
			 "data.*.have_icon.boolean"                        =>  trans('have icon should be a boolean'), 
			 "data.*.have_icon.required"                       =>  trans('have icon is required'), 
			 "data.*.have_video.boolean"                       =>  trans('have video should be a boolean'), 
			 "data.*.have_video.required"                      =>  trans('have video is required'), 
			 "data.*.have_link.boolean"                        =>  trans('have link should be a boolean'), 
			 "data.*.have_link.required"                       =>  trans('have link is required'), 
			 "data.*.details.array"                            =>  trans('details is not array'), 
			 "data.*.details.*.translations.en.name.string"    =>  trans('name should be a string'), 
			 "data.*.details.*.translations.en.name.required"  =>  trans('name is required'), 
			 "data.*.details.*.translations.ar.name.string"    =>  trans('name should be a string'), 
			 "data.*.details.*.translations.ar.name.required"  =>  trans('name is required'), 
			 "data.*.details.*.video.string"                   =>  trans('video should be a string'), 
			 "data.*.details.*.video.required"                 =>  trans('video is required'), 
			 "data.*.details.*.link.string"                    =>  trans('link should be a string'), 
			 "data.*.details.*.link.required"                  =>  trans('link is required'), 
			 "data.*.details.*.cms_pages_id.exists"            =>  trans('cms pages is not Valid'), 
			 "data.*.details.*.cms_pages_id.required"          =>  trans('cms pages is required'), 
			 "data.*.details.required"                         =>  trans('details is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
