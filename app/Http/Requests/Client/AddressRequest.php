<?php

namespace App\Http\Requests\Client;

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
            'name.required' => trans('validation.required', ['attribute' => trans('client.address_name')]),
            'name.string' => trans('validation.string', ['attribute' => trans('client.address_name')]),
            'name.max' => trans('validation.max.string', ['attribute' => trans('client.address_name'), 'max' => 255]),
            
            'location.required' => trans('validation.required', ['attribute' => trans('client.address')]),
            'location.string' => trans('validation.string', ['attribute' => trans('client.address')]),
            'location.max' => trans('validation.max.string', ['attribute' => trans('client.address'), 'max' => 500]),
            
            'city_id.required' => trans('validation.required', ['attribute' => trans('client.city')]),
            'city_id.exists' => trans('validation.exists', ['attribute' => trans('client.city')]),
            
            'district_id.required' => trans('validation.required', ['attribute' => trans('client.district')]),
            'district_id.exists' => trans('validation.exists', ['attribute' => trans('client.district')]),
            
            'lat.required' => trans('validation.required', ['attribute' => trans('client.lat')]),
            'lat.numeric' => trans('validation.numeric', ['attribute' => trans('client.lat')]),
            
            'lng.required' => trans('validation.required', ['attribute' => trans('client.lng')]),
            'lng.numeric' => trans('validation.numeric', ['attribute' => trans('client.lng')]),
            
        ];
    }
} 