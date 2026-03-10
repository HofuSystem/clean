<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ContactRequestsRequest extends FormRequest
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
			 "name"        =>  ['required','string'],
			 "phone"       =>  ['required','string'],
			 "email"       =>  ['nullable','email'],
			 "type"  =>  ['nullable','string'],
			 "notes"        =>  ['required','string'],
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
			 "name.string"          =>  trans('name should be a string'),
			 "name.required"        =>  trans('name is required'),
			 "phone.string"         =>  trans('phone should be a string'),
			 "phone.required"       =>  trans('phone is required'),
			 "email.email"          =>  trans('email should be a email'),
			 "email.required"       =>  trans('email is required'),
			 "service_id.exists"    =>  trans('service is not Valid'),
			 "service_id.required"  =>  trans('service is required'),
			 "date.date"            =>  trans('date should be a date'),
			 "date.required"        =>  trans('date is required'),
			 "time.time"            =>  trans('time should be a time'),
			 "time.required"        =>  trans('time is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
