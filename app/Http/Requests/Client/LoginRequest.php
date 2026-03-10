<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|max:20',
            'password' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone.required' => trans('validation.required', ['attribute' => trans('auth.phone_placeholder')]),
            'phone.string' => trans('validation.string', ['attribute' => trans('auth.phone_placeholder')]),
            'phone.max' => trans('validation.max.string', ['attribute' => trans('auth.phone_placeholder'), 'max' => 20]),
            
            'password.required' => trans('validation.required', ['attribute' => trans('auth.password_placeholder')]),
            'password.string' => trans('validation.string', ['attribute' => trans('auth.password_placeholder')]),
            'password.min' => trans('validation.min.string', ['attribute' => trans('auth.password_placeholder'), 'min' => 8]),
        ];
    }
} 