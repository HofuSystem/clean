<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $rules = [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'type' => 'required|string',
            'monthly_items' => 'required|string',
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
            'company_name.required' => trans('validation.required', ['attribute' => trans('auth.company_name_placeholder')]),
            'company_name.string' => trans('validation.string', ['attribute' => trans('auth.company_name_placeholder')]),
            'company_name.max' => trans('validation.max.string', ['attribute' => trans('auth.company_name_placeholder'), 'max' => 255]),
            
            'contact_person.required' => trans('validation.required', ['attribute' => trans('auth.contact_person_placeholder')]),
            'contact_person.string' => trans('validation.string', ['attribute' => trans('auth.contact_person_placeholder')]),
            'contact_person.max' => trans('validation.max.string', ['attribute' => trans('auth.contact_person_placeholder'), 'max' => 255]),
            
            'phone.required' => trans('validation.required', ['attribute' => trans('auth.phone_placeholder')]),
            'phone.string' => trans('validation.string', ['attribute' => trans('auth.phone_placeholder')]),
            'phone.max' => trans('validation.max.string', ['attribute' => trans('auth.phone_placeholder'), 'max' => 20]),
            'phone.unique' => trans('validation.unique', ['attribute' => trans('auth.phone_placeholder')]),
            
            'type.required' => trans('validation.required', ['attribute' => trans('auth.type_placeholder')]),
            'type.string' => trans('validation.string', ['attribute' => trans('auth.type_placeholder')]),
            
            'monthly_items.required' => trans('validation.required', ['attribute' => trans('auth.monthly_items_placeholder')]),
            'monthly_items.numeric' => trans('validation.numeric', ['attribute' => trans('auth.monthly_items_placeholder')]),
            
        ];
    }
} 