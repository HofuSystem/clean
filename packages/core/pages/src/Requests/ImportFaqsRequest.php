<?php

namespace Core\Pages\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportFaqsRequest extends FormRequest
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
			 "data.*.translations.en.question"  =>  ['required','string'],
			 "data.*.translations.ar.question"  =>  ['required','string'],
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
			 "data.*.translations.en.question.string"    =>  trans('question should be a string'),
			 "data.*.translations.en.question.required"  =>  trans('question is required'),
			 "data.*.translations.ar.question.string"    =>  trans('question should be a string'),
			 "data.*.translations.ar.question.required"  =>  trans('question is required'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
