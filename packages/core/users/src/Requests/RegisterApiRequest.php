<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterApiRequest extends FormRequest
{
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
        $rules = [
            'name'          => ['required', 'string'],
            'phone'         => ['phoneNumber:country_key'],
            'username'      => [],
            'email'         => ['email'],
            'country_key'   => ['required'],
            'password'      => ['required', 'min:8'],
            'device_info'   => ['required','jsonArray','jsonHas:device_unique_id'],
        ];
        if((!request()->has('phone') or request()->phone == null) and (!request()->has('email') or request()->email == null)){
            $rules['username'][]  ="required";
        }
        if((!request()->has('username') or request()->username == null) and (!request()->has('email') or request()->email == null)){
            $rules['phone'][]     ="required";
        }
        if((!request()->has('username') or request()->username == null)  and (!request()->has('phone') or request()->phone == null) ){
            $rules['email'][]     ="required";
        }
        if(!request()->has("code")) {
            $rules['email'][]     ="unique:users";
            $rules['username'][]  ="unique:users";
            $rules['phone'][]     ="unique:users";
        }
      return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'     => trans('the name field is required'),
            'name.string'       => trans('the name field must be string'),
            'phone.required'    => trans('the phone field is required'),
            'phone.numeric'     => trans('the phone field must be numeric'),
            'phone.unique'      => trans('the phone field must be unique'),
            'phone.phoneNumber' => trans('the phone field must be phoneNumber'),
            'username.required' => trans('the username field is required'),
            'username.unique'   => trans('the username field must be unique'),
            'email.unique'      => trans('the email field must be unique'),
            'email.email'       => trans('the email field must be email'),
            'email.required'        => trans('the email field must is required'),
            'country_key.required'  => trans('the country_key field is required'),
            'password.required'     => trans('the password field is required'),
            'password.min'          => trans('the password is short'),
            'device_info.required'  => trans('the device_info field is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ], 200));
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->merge(request()->all());
        });
    }

}
