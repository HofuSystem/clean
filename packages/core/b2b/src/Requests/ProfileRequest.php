<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
        $userId = auth()->id();
        
        $rules = [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'address' => 'required|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'fullname.required' => trans('validation.required', ['attribute' => trans('fullname')]),
            'fullname.string' => trans('validation.string', ['attribute' => trans('fullname')]),
            'fullname.max' => trans('validation.max.string', ['attribute' => trans('fullname'), 'max' => 255]),
            
            'email.required' => trans('validation.required', ['attribute' => trans('email')]),
            'email.email' => trans('validation.email', ['attribute' => trans('email')]),
            'email.unique' => trans('validation.unique', ['attribute' => trans('email')]),
            
            'phone.required' => trans('validation.required', ['attribute' => trans('phone')]),
            'phone.string' => trans('validation.string', ['attribute' => trans('phone')]),
            'phone.max' => trans('validation.max.string', ['attribute' => trans('phone'), 'max' => 20]),
            'phone.unique' => trans('validation.unique', ['attribute' => trans('phone')]),
            
            'address.required' => trans('validation.required', ['attribute' => trans('address')]),
            'address.string' => trans('validation.string', ['attribute' => trans('address')]),
            'address.max' => trans('validation.max.string', ['attribute' => trans('address'), 'max' => 500]),
            
            'business_field.required' => trans('validation.required', ['attribute' => trans('business_field')]),
            'business_field.string' => trans('validation.string', ['attribute' => trans('business_field')]),
            'business_field.in' => trans('validation.in', ['attribute' => trans('business_field')]),
            
            'other_business_field.required' => trans('validation.required', ['attribute' => trans('other_business_field')]),
            'other_business_field.string' => trans('validation.string', ['attribute' => trans('other_business_field')]),
            'other_business_field.max' => trans('validation.max.string', ['attribute' => trans('other_business_field'), 'max' => 255]),
            
            'password.string' => trans('validation.string', ['attribute' => trans('password')]),
            'password.min' => trans('validation.min.string', ['attribute' => trans('password'), 'min' => 8]),
            'password.confirmed' => trans('validation.confirmed', ['attribute' => trans('password')]),
            
            'password_confirmation.string' => trans('validation.string', ['attribute' => trans('password_confirmation')]),
            'password_confirmation.min' => trans('validation.min.string', ['attribute' => trans('password_confirmation'), 'min' => 8]),
            
            'image.image' => trans('validation.image', ['attribute' => trans('image')]),
            'image.mimes' => trans('validation.mimes', ['attribute' => trans('image'), 'values' => 'jpeg,png,jpg,gif']),
            'image.max' => trans('validation.max.file', ['attribute' => trans('image'), 'max' => 2048]),
        ];
    }
} 