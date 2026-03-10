<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class BusinessesRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "translations.en.title"  =>  ['nullable', 'string'],
            "translations.ar.title"  =>  ['nullable', 'string'],
            "description.en.title"  =>  ['nullable', 'string'],
            "description.ar.title"  =>  ['nullable', 'string'],
            "icon"                  =>  ['required', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "translations.en.title.string"    =>  trans('title should be a string'),
            "translations.en.title.required"  =>  trans('title is required'),
            "translations.ar.title.string"    =>  trans('title should be a string'),
            "translations.ar.title.required"  =>  trans('title is required'),
            "translations.en.description.string"    =>  trans('description should be a string'),
            "translations.en.description.required"  =>  trans('description is required'),
            "translations.ar.description.string"    =>  trans('description should be a string'),
            "translations.ar.description.required"  =>  trans('description is required'),
            "count.string"                   =>  trans('count should be a string'),
            "count.required"                  =>  trans('count is required'),

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
