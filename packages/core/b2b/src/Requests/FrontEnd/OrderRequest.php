<?php

namespace Core\B2B\Requests\FrontEnd;

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
            'branch_id' => 'required_without:receiving_address_id|nullable|exists:company_branches,id',
            'receiving_address_id' => 'required_without:branch_id|nullable|exists:addresses,id',
            'receiving_date' => 'required|date',
            'receiving_time' => 'required|exists:category_date_times,id',
            'delivery_address_id' => 'nullable|exists:addresses,id',
            'delivery_date' => 'required|date',
            'delivery_time' => 'required|exists:category_date_times,id',
            'type' => 'required|in:clothes,fastorder,sales,services,host,care,selfcare,maidflex,maidscheduled,maidPackage,maidoffer',
            'b2b_type' => 'required|in:company,client',
            'customer_name' => 'required_if:b2b_type,client|nullable|string|max:255',
            'customer_phone' => 'required_if:b2b_type,client|nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:1000',
            'service_type' => 'required|string',
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