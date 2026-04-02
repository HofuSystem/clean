<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryAppFeaturesRequest extends FormRequest
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
            "translations.en.title" => ['required', 'string'],
            "translations.ar.title" => ['required', 'string'],
            "category_id" => ['required', 'exists:categories,id'],
            "section" => ['required', 'in:mainFeature,reviewsCount,reviewsRate,intro,secFeaures,whyus,included'],
            "image" => ['nullable', 'string'],
            "value" => ['nullable', 'numeric'],
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
            "translations.en.title.required" => trans('title is required'),
            "translations.ar.title.required" => trans('title is required'),
            "category_id.required" => trans('category is required'),
            "category_id.exists" => trans('category is not valid'),
            "section.required" => trans('section is required'),
            "section.in" => trans('section is not valid'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->returnValidationError($validator));
    }
}
