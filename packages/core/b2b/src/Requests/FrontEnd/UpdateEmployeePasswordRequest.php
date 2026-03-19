<?php

namespace Core\B2B\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeePasswordRequest extends FormRequest
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
            'password' => 'required|string|min:6|confirmed',
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
            'password.required' => trans('validation.required', ['attribute' => trans('client.password')]),
            'password.string' => trans('validation.string', ['attribute' => trans('client.password')]),
            'password.min' => trans('validation.min.string', ['attribute' => trans('client.password'), 'min' => 6]),
            'password.confirmed' => trans('validation.confirmed', ['attribute' => trans('client.password')]),
        ];
    }
}
