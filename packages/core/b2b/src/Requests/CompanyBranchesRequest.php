<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CompanyBranchesRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'location'    => ['nullable', 'string', 'max:255'],
            'lat'         => ['nullable', 'string', 'max:50'],
            'lng'         => ['nullable', 'string', 'max:50'],
            'city_id'     => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'user_id'     => ['nullable', 'exists:users,id'],
            'is_default'  => ['nullable', 'boolean'],
            'is_active'  => ['nullable', 'boolean'],
            'company_id'  => ['required', 'exists:companies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => trans('branch name is required'),
            'company_id.required' => trans('company is required'),
            'company_id.exists'   => trans('selected company is not valid'),
            'city_id.exists'      => trans('selected city is not valid'),
            'district_id.exists'  => trans('selected district is not valid'),
            'user_id.exists'      => trans('selected user is not valid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
