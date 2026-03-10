<?php

namespace App\Http\Requests\Client;

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
            'order_type' => 'required|in:me,customer',
            'customer_name' => 'required_if:order_type,customer|nullable|string|max:255',
            'customer_phone' => 'required_if:order_type,customer|nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
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
            'receiving_address_id.required' => trans('validation.required', ['attribute' => trans('client.receiving_address_id')]),
            'receiving_address_id.exists' => trans('validation.exists', ['attribute' => trans('client.receiving_address_id')]),
            'receiving_date.date' => trans('validation.date', ['attribute' => trans('client.receiving_date')]),
            'receiving_time.required' => trans('validation.required', ['attribute' => trans('client.receiving_time')]),
            'receiving_time.exists' => trans('validation.exists', ['attribute' => trans('client.receiving_time')]),
            'delivery_address_id.required' => trans('validation.required', ['attribute' => trans('client.delivery_address_id')]),
            'delivery_address_id.exists' => trans('validation.exists', ['attribute' => trans('client.delivery_address_id')]),
            'delivery_date.date' => trans('validation.date', ['attribute' => trans('client.delivery_date')]),
            'delivery_time.required' => trans('validation.required', ['attribute' => trans('client.delivery_time')]),
            'delivery_time.exists' => trans('validation.exists', ['attribute' => trans('client.delivery_time')]),

        ];
    }
}