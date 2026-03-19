<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CompanyEmployeesRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'       => ['required', 'exists:users,id'],
            'company_id'    => ['required', 'exists:companies,id'],
            'permission_id' => ['required', 'exists:company_permissions,id'],
            'branch_id'     => ['nullable', 'exists:company_branches,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => trans('user is required'),
            'user_id.exists'         => trans('selected user is not valid'),
            'company_id.required'    => trans('company is required'),
            'company_id.exists'      => trans('selected company is not valid'),
            'permission_id.required' => trans('permission is required'),
            'permission_id.exists'   => trans('selected permission is not valid'),
            'branch_id.exists'       => trans('selected branch is not valid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
