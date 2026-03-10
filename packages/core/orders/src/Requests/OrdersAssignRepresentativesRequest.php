<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class OrdersAssignRepresentativesRequest extends FormRequest
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
      'type'              => 'required|string|in:delivery,technical,receiver',
      'representative_id' => 'required|exists:users,id',
      'for'               => 'required|string',

    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array
   */


  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnValidationError($validator));
  }
}
