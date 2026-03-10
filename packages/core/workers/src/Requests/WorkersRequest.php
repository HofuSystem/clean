<?php

namespace Core\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class WorkersRequest extends FormRequest
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
			 "name"              =>  ['required','string'], 
			 "phone"             =>  ['nullable','string'], 
			 "email"             =>  ['nullable','email'], 
			 "years_experience"  =>  ['nullable','numeric'], 
			 "address"           =>  ['nullable','string'], 
			 "birth_date"        =>  ['nullable','date'], 
			 "hour_price"        =>  ['nullable','numeric'], 
			 "gender"            =>  ['nullable','in:male,female'], 
			 "status"            =>  ['nullable','in:active,not-active'], 
			 "identity"          =>  ['nullable','in:passport,id,driver-licence'], 
			 "nationality_id"    =>  ['nullable','exists:nationalities,id'], 
			 "city_id"           =>  ['nullable','exists:cities,id'], 
			 "categories"        =>  ['nullable','array'], 
			 "categories.*"      =>  ['exists:categories,id'], 
			 "leader_id"         =>  ['exists:users,id'], 
			 "workdays"          =>  ['nullable','array'], 
			 "workdays.*.date"   =>  ['required','date'], 
			 "workdays.*.type"   =>  ['required','in:absence,attendees'], 
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
			 "name.string"                    =>  trans('name should be a string'), 
			 "name.required"                  =>  trans('name is required'), 
			 "phone.string"                   =>  trans('phone should be a string'), 
			 "phone.required"                 =>  trans('phone is required'), 
			 "email.email"                    =>  trans('email should be a email'), 
			 "email.required"                 =>  trans('email is required'), 
			 "years_experience.numeric"       =>  trans('years experience should be a number'), 
			 "years_experience.required"      =>  trans('years experience is required'), 
			 "address.string"                 =>  trans('address should be a string'), 
			 "address.required"               =>  trans('address is required'), 
			 "birth_date.date"                =>  trans('birth date should be a date'), 
			 "birth_date.required"            =>  trans('birth date is required'), 
			 "hour_price.numeric"             =>  trans('hour price should be a number'), 
			 "hour_price.required"            =>  trans('hour price is required'), 
			 "gender.in"                      =>  trans('gender is not allowed'), 
			 "gender.required"                =>  trans('gender is required'), 
			 "status.in"                      =>  trans('status is not allowed'), 
			 "status.required"                =>  trans('status is required'), 
			 "identity.in"                    =>  trans('identity is not allowed'), 
			 "identity.required"              =>  trans('identity is required'), 
			 "nationality_id.exists"          =>  trans('nationality is not Valid'), 
			 "nationality_id.required"        =>  trans('nationality is required'), 
			 "city_id.exists"                 =>  trans('city is not Valid'), 
			 "city_id.required"               =>  trans('city is required'), 
			 "categories.array"               =>  trans('categories is not array'), 
			 "categories.*.exists.*"          =>  trans('categories is not Valid'), 
			 "categories.required"            =>  trans('categories is required'), 
			 "leader_id.exists"             =>  trans('leaders is not Valid'), 
			 "workdays.array"                 =>  trans('work days is not array'), 
			 "workdays.*.worker_id.exists"    =>  trans('worker is not Valid'), 
			 "workdays.*.worker_id.required"  =>  trans('worker is required'), 
			 "workdays.*.date.date"           =>  trans('date should be a date'), 
			 "workdays.*.date.required"       =>  trans('date is required'), 
			 "workdays.*.type.in"             =>  trans('type is not allowed'), 
			 "workdays.*.type.required"       =>  trans('type is required'), 
			 "workdays.required"              =>  trans('work days is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
