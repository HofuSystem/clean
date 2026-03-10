<?php

namespace Core\Orders\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class UpdateOrderRequest extends FormRequest
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
      'order_total'   => ['nullable','numeric'],
      'address_id'    => ['required','numeric','exists:addresses,id'],
      'type'          => ['required'],
      'category_id'   => ['nullable'],
    ];
  }



  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnErrorMessage($validator->errors()->first(),$validator->errors(),['status'=>'fail'],422));
  }
}
