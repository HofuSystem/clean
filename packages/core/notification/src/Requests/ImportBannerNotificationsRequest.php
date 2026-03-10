<?php

namespace Core\Notification\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportBannerNotificationsRequest extends FormRequest
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
			 "data.*.publish_date"      =>  ['required','date'], 
			 "data.*.expired_date"      =>  ['nullable','date'], 
			 "data.*.next_vision_hour"  =>  ['required','numeric'], 
			 "data.*.status"            =>  ['required','in:active,not-active'], 
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
			 "data.*.publish_date.date"          =>  trans('publish date should be a date'), 
			 "data.*.publish_date.required"      =>  trans('publish date is required'), 
			 "data.*.expired_date.date"          =>  trans('expired date should be a date'), 
			 "data.*.expired_date.required"      =>  trans('expired date is required'), 
			 "data.*.next_vision_hour.numeric"   =>  trans('next vision hour should be a number'), 
			 "data.*.next_vision_hour.required"  =>  trans('next vision hour is required'), 
			 "data.*.status.in"                  =>  trans('status is not allowed'), 
			 "data.*.status.required"            =>  trans('status is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
