<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class RolesRequest extends FormRequest
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
      "translations.en.title"  =>  ['required', 'string'],
      "translations.ar.title"  =>  ['required', 'string'],
      "name"                   =>  ['required', 'unique:roles,name,' . $this->id, 'string'],
      "permissions"            =>  ['nullable', 'array'],
      "permissions.*"          =>  ['exists:permissions,name'],
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
      "name.string"                     =>  trans('name should be a string'),
      "name.required"                   =>  trans('name is required'),
      "name.unique"                     =>  trans('name should be unique'),
      "permissions.array"               =>  trans('permissions is not array'),
      "permissions.*.exists.*"          =>  trans('permissions is not Valid'),
      "permissions.required"            =>  trans('permissions is required'),
    ];
  }

  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnValidationError($validator));
  }
}
