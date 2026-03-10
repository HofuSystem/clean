<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginApiRequest extends FormRequest
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
            'username' => 'required',
            'password' => 'required',
            'device_info' => ['required','jsonArray','jsonHas:device_unique_id'],

        ];

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
            'username.required'     => trans('the login method field is required'),
            'password.required'     => trans('the password field is required'),
            'device_info.required'  => trans('the device_info field is required'),
            'device_info.json_has'   => trans('the device_info empty'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'error',
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
