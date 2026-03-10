<?php

namespace Core\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class BannerNotificationsRequest extends FormRequest
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
			 "publish_date"      =>  ['required','date'], 
			 "expired_date"      =>  ['nullable','date'], 
			 "next_vision_hour"  =>  ['required','numeric'], 
			 "status"            =>  ['required','in:active,not-active'], 
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
			 "publish_date.date"          =>  trans('publish date should be a date'), 
			 "publish_date.required"      =>  trans('publish date is required'), 
			 "expired_date.date"          =>  trans('expired date should be a date'), 
			 "expired_date.required"      =>  trans('expired date is required'), 
			 "next_vision_hour.numeric"   =>  trans('next vision hour should be a number'), 
			 "next_vision_hour.required"  =>  trans('next vision hour is required'), 
			 "status.in"                  =>  trans('status is not allowed'), 
			 "status.required"            =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
