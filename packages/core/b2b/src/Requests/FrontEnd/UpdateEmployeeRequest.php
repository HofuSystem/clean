<?php

namespace Core\B2B\Requests\FrontEnd;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:company_permissions,id',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:company_branches,id',
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
            'permission_ids.required' => trans('validation.required', ['attribute' => trans('client.permissions')]),
            'permission_ids.array' => trans('validation.array', ['attribute' => trans('client.permissions')]),
            'permission_ids.*.exists' => trans('validation.exists', ['attribute' => trans('client.permission')]),
            'branch_ids.exists' => trans('validation.exists', ['attribute' => trans('client.branch')]),
        ];
    }
}
