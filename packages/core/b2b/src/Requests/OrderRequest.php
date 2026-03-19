<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'receiving_address_id' => 'required|exists:addresses,id',
            'receiving_date' => 'nullable|date',
            'receiving_time' => 'required|exists:category_date_times,id',
            'delivery_address_id' => 'required|exists:addresses,id',
            'delivery_date' => 'required|date',
            'delivery_time' => 'required|exists:category_date_times,id',

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
            'receiving_address_id.required' => trans('validation.required', ['attribute' => trans('receiving_address_id')]),
            'receiving_address_id.exists' => trans('validation.exists', ['attribute' => trans('receiving_address_id')]),
            'receiving_date.date' => trans('validation.date', ['attribute' => trans('receiving_date')]),
            'receiving_time.required' => trans('validation.required', ['attribute' => trans('receiving_time')]),
            'receiving_time.exists' => trans('validation.exists', ['attribute' => trans('receiving_time')]),
            'delivery_address_id.required' => trans('validation.required', ['attribute' => trans('delivery_address_id')]),
            'delivery_address_id.exists' => trans('validation.exists', ['attribute' => trans('delivery_address_id')]),
            'delivery_date.date' => trans('validation.date', ['attribute' => trans('delivery_date')]),
            'delivery_time.required' => trans('validation.required', ['attribute' => trans('delivery_time')]),
            'delivery_time.exists' => trans('validation.exists', ['attribute' => trans('delivery_time')]),

        ];
    }
} 