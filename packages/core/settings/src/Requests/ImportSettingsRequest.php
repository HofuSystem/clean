<?php

namespace Core\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportSettingsRequest extends FormRequest
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
			 "data.*.translations.en.text"         =>  ['required','string'], 
			 "data.*.translations.ar.text"         =>  ['required','string'], 
			 "data.*.email"                        =>  ['nullable','email'], 
			 "data.*.phone"                        =>  ['nullable','string'], 
			 "data.*.whatsapp"                     =>  ['nullable','string'], 
			 "data.*.translations.en.policy"       =>  ['required','string'], 
			 "data.*.translations.ar.policy"       =>  ['required','string'], 
			 "data.*.translations.en.terms"        =>  ['required','string'], 
			 "data.*.translations.ar.terms"        =>  ['required','string'], 
			 "data.*.translations.en.description"  =>  ['required','string'], 
			 "data.*.translations.ar.description"  =>  ['required','string'], 
			 "data.*.translations.en.about_us"     =>  ['required','string'], 
			 "data.*.translations.ar.about_us"     =>  ['required','string'], 
			 "data.*.facebook"                     =>  ['nullable','string'], 
			 "data.*.twitter"                      =>  ['nullable','string'], 
			 "data.*.youtube"                      =>  ['nullable','string'], 
			 "data.*.instagram"                    =>  ['nullable','string'], 
			 "data.*.android_link"                 =>  ['nullable','string'], 
			 "data.*.ios_link"                     =>  ['nullable','string'], 
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
			 "data.*.translations.en.text.string"           =>  trans('name should be a string'), 
			 "data.*.translations.en.text.required"         =>  trans('name is required'), 
			 "data.*.translations.ar.text.string"           =>  trans('name should be a string'), 
			 "data.*.translations.ar.text.required"         =>  trans('name is required'), 
			 "data.*.email.email"                           =>  trans('email should be a email'), 
			 "data.*.email.required"                        =>  trans('email is required'), 
			 "data.*.phone.string"                          =>  trans('phone should be a string'), 
			 "data.*.phone.required"                        =>  trans('phone is required'), 
			 "data.*.whatsapp.string"                       =>  trans('whatsapp should be a string'), 
			 "data.*.whatsapp.required"                     =>  trans('whatsapp is required'), 
			 "data.*.translations.en.policy.string"         =>  trans('policy should be a string'), 
			 "data.*.translations.en.policy.required"       =>  trans('policy is required'), 
			 "data.*.translations.ar.policy.string"         =>  trans('policy should be a string'), 
			 "data.*.translations.ar.policy.required"       =>  trans('policy is required'), 
			 "data.*.translations.en.terms.string"          =>  trans('terms  should be a string'), 
			 "data.*.translations.en.terms.required"        =>  trans('terms  is required'), 
			 "data.*.translations.ar.terms.string"          =>  trans('terms  should be a string'), 
			 "data.*.translations.ar.terms.required"        =>  trans('terms  is required'), 
			 "data.*.translations.en.description.string"    =>  trans('description should be a string'), 
			 "data.*.translations.en.description.required"  =>  trans('description is required'), 
			 "data.*.translations.ar.description.string"    =>  trans('description should be a string'), 
			 "data.*.translations.ar.description.required"  =>  trans('description is required'), 
			 "data.*.translations.en.about_us.string"       =>  trans('about us should be a string'), 
			 "data.*.translations.en.about_us.required"     =>  trans('about us is required'), 
			 "data.*.translations.ar.about_us.string"       =>  trans('about us should be a string'), 
			 "data.*.translations.ar.about_us.required"     =>  trans('about us is required'), 
			 "data.*.facebook.string"                       =>  trans('Facebook should be a string'), 
			 "data.*.facebook.required"                     =>  trans('Facebook is required'), 
			 "data.*.twitter.string"                        =>  trans('Twitter should be a string'), 
			 "data.*.twitter.required"                      =>  trans('Twitter is required'), 
			 "data.*.youtube.string"                        =>  trans('Youtube should be a string'), 
			 "data.*.youtube.required"                      =>  trans('Youtube is required'), 
			 "data.*.instagram.string"                      =>  trans('Instagram should be a string'), 
			 "data.*.instagram.required"                    =>  trans('Instagram is required'), 
			 "data.*.android_link.string"                   =>  trans('android link should be a string'), 
			 "data.*.android_link.required"                 =>  trans('android link is required'), 
			 "data.*.ios_link.string"                       =>  trans('ios link should be a string'), 
			 "data.*.ios_link.required"                     =>  trans('ios link is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
