<?php

namespace Core\B2B\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CompanyPermissionsRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'slug'                 => ['required', 'string', 'max:255', 'unique:company_permissions,slug,' . $id],
            'translations.en.name'        => ['required', 'string', 'max:255'],
            'translations.ar.name'        => ['required', 'string', 'max:255'],
            'translations.en.description' => ['nullable', 'string'],
            'translations.ar.description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.required'                 => trans('slug is required'),
            'slug.unique'                   => trans('slug already exists'),
            'translations.en.name.required' => trans('English name is required'),
            'translations.ar.name.required' => trans('Arabic name is required'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
