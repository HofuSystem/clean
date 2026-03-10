<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportCountersRequest extends FormRequest
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
			 "data.*.translations.en.title"  =>  ['nullable','string'],
			 "data.*.translations.ar.title"  =>  ['nullable','string'],
			 "data.*.count"                  =>  ['required','numeric'],
			 "data.*.is_active"              =>  ['nullable','boolean'],
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
			 "data.*.translations.en.title.string"    =>  trans('title should be a string'),
			 "data.*.translations.en.title.required"  =>  trans('title is required'),
			 "data.*.translations.ar.title.string"    =>  trans('title should be a string'),
			 "data.*.translations.ar.title.required"  =>  trans('title is required'),
			 "data.*.count.numeric"                   =>  trans('count should be a number'),
			 "data.*.count.required"                  =>  trans('count is required'),
			 "data.*.is_active.boolean"               =>  trans('is active should be a boolean'),
			 "data.*.is_active.required"              =>  trans('is active is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
