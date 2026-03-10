<?php

namespace Core\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportWorkersRequest extends FormRequest
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
			 "data.*.name"              =>  ['required','string'], 
			 "data.*.phone"             =>  ['nullable','string'], 
			 "data.*.email"             =>  ['nullable','email'], 
			 "data.*.years_experience"  =>  ['nullable','numeric'], 
			 "data.*.address"           =>  ['nullable','string'], 
			 "data.*.birth_date"        =>  ['nullable','date'], 
			 "data.*.hour_price"        =>  ['nullable','numeric'], 
			 "data.*.gender"            =>  ['nullable','in:male,female'], 
			 "data.*.status"            =>  ['nullable','in:active,not-active'], 
			 "data.*.identity"          =>  ['nullable','in:passport,id,driver-licence'], 
			 "data.*.nationality_id"    =>  ['nullable','exists:nationalities,id'], 
			 "data.*.city_id"           =>  ['nullable','exists:cities,id'], 
			 "data.*.categories"        =>  ['nullable','array'], 
			 "data.*.categories.*"      =>  ['exists:categories,id'], 
			 "data.*.leader_id"         =>  ['exists:users,id'], 
			 "data.*.workdays"          =>  ['nullable','array'], 
			 "data.*.workdays.*.date"   =>  ['required','date'], 
			 "data.*.workdays.*.type"   =>  ['required','in:absence,attendees'], 
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
			 "data.*.name.string"                    =>  trans('name should be a string'), 
			 "data.*.name.required"                  =>  trans('name is required'), 
			 "data.*.phone.string"                   =>  trans('phone should be a string'), 
			 "data.*.phone.required"                 =>  trans('phone is required'), 
			 "data.*.email.email"                    =>  trans('email should be a email'), 
			 "data.*.email.required"                 =>  trans('email is required'), 
			 "data.*.years_experience.numeric"       =>  trans('years experience should be a number'), 
			 "data.*.years_experience.required"      =>  trans('years experience is required'), 
			 "data.*.address.string"                 =>  trans('address should be a string'), 
			 "data.*.address.required"               =>  trans('address is required'), 
			 "data.*.birth_date.date"                =>  trans('birth date should be a date'), 
			 "data.*.birth_date.required"            =>  trans('birth date is required'), 
			 "data.*.hour_price.numeric"             =>  trans('hour price should be a number'), 
			 "data.*.hour_price.required"            =>  trans('hour price is required'), 
			 "data.*.gender.in"                      =>  trans('gender is not allowed'), 
			 "data.*.gender.required"                =>  trans('gender is required'), 
			 "data.*.status.in"                      =>  trans('status is not allowed'), 
			 "data.*.status.required"                =>  trans('status is required'), 
			 "data.*.identity.in"                    =>  trans('identity is not allowed'), 
			 "data.*.identity.required"              =>  trans('identity is required'), 
			 "data.*.nationality_id.exists"          =>  trans('nationality is not Valid'), 
			 "data.*.nationality_id.required"        =>  trans('nationality is required'), 
			 "data.*.city_id.exists"                 =>  trans('city is not Valid'), 
			 "data.*.city_id.required"               =>  trans('city is required'), 
			 "data.*.categories.array"               =>  trans('categories is not array'), 
			 "data.*.categories.*.exists.*"          =>  trans('categories is not Valid'), 
			 "data.*.categories.required"            =>  trans('categories is required'), 
			 "data.*.leader_id.exists"             =>  trans('leaders is not Valid'), 
			 "data.*.workdays.array"                 =>  trans('work days is not array'), 
			 "data.*.workdays.*.worker_id.exists"    =>  trans('worker is not Valid'), 
			 "data.*.workdays.*.worker_id.required"  =>  trans('worker is required'), 
			 "data.*.workdays.*.date.date"           =>  trans('date should be a date'), 
			 "data.*.workdays.*.date.required"       =>  trans('date is required'), 
			 "data.*.workdays.*.type.in"             =>  trans('type is not allowed'), 
			 "data.*.workdays.*.type.required"       =>  trans('type is required'), 
			 "data.*.workdays.required"              =>  trans('work days is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
