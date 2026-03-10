<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class SlidersRequest extends FormRequest
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
      "image_en" => ['required', 'string', 'max:255'],
      "image_ar" => ['required', 'string', 'max:255'],
      "category_id" => ['required', 'exists:categories,id'],
      "link" => ['nullable', 'string', 'max:255'],
      "type" => ['required', 'in:services,sales,clothes,host,maid'],
      "status" => ['required', 'in:active,not-active'],
      "city_id" => ['nullable', 'exists:cities,id'],
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
      "category_id.exists" => trans('category is not Valid'),
      "category_id.required" => trans('category is required'),
      "type.in" => trans('type is not allowed'),
      "type.required" => trans('type is required'),
      "status.in" => trans('status is not allowed'),
      "status.required" => trans('status is required'),
      "city_id.exists" => trans('city is not Valid'),
      "city_id.required" => trans('city is required'),
    ];

  }

  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnValidationError($validator));
  }

}
