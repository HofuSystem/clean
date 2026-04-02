<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:500',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
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
            'name.required' => trans('validation.required', ['attribute' => trans('address_name')]),
            'name.string' => trans('validation.string', ['attribute' => trans('address_name')]),
            'name.max' => trans('validation.max.string', ['attribute' => trans('address_name'), 'max' => 255]),
            
            'location.required' => trans('validation.required', ['attribute' => trans('address')]),
            'location.string' => trans('validation.string', ['attribute' => trans('address')]),
            'location.max' => trans('validation.max.string', ['attribute' => trans('address'), 'max' => 500]),
            
            'city_id.required' => trans('validation.required', ['attribute' => trans('city')]),
            'city_id.exists' => trans('validation.exists', ['attribute' => trans('city')]),
            
            'district_id.required' => trans('validation.required', ['attribute' => trans('district')]),
            'district_id.exists' => trans('validation.exists', ['attribute' => trans('district')]),
            
            'lat.required' => trans('validation.required', ['attribute' => trans('lat')]),
            'lat.numeric' => trans('validation.numeric', ['attribute' => trans('lat')]),
            
            'lng.required' => trans('validation.required', ['attribute' => trans('lng')]),
            'lng.numeric' => trans('validation.numeric', ['attribute' => trans('lng')]),
            
        ];
    }
} 