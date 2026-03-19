<?php

namespace Core\B2B\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'phone' => 'required|string',
            'fullname' => 'required|string|max:255',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:company_permissions,id',
            'branch_id' => 'nullable|exists:company_branches,id',
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
            'phone.required' => trans('validation.required', ['attribute' => trans('client.phone')]),
            'fullname.required' => trans('validation.required', ['attribute' => trans('client.fullname')]),
            'permission_ids.required' => trans('validation.required', ['attribute' => trans('client.permissions')]),
            'permission_ids.array' => trans('validation.array', ['attribute' => trans('client.permissions')]),
            'permission_ids.*.exists' => trans('validation.exists', ['attribute' => trans('client.permission')]),
            'branch_id.exists' => trans('validation.exists', ['attribute' => trans('client.branch')]),
        ];
    }
}
