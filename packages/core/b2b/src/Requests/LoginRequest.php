<?php

namespace Core\B2B\Requests;

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
            'email' => 'required|string|max:255',
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
            'email.required' => trans('validation.required', ['attribute' => trans('phone_email_label')]),
            'email.string' => trans('validation.string', ['attribute' => trans('phone_email_label')]),
            'email.max' => trans('validation.max.string', ['attribute' => trans('phone_email_label'), 'max' => 255]),

            'password.required' => trans('validation.required', ['attribute' => trans('password_label')]),
            'password.string' => trans('validation.string', ['attribute' => trans('password_label')]),
        ];
    }
}