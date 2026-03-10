<?php

namespace Core\Orders\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class PayFastOrderRequest extends FormRequest
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
    $data = [
      'pay_type'                    => 'required|in:card,cash,wallet',
      'paid'                        => 'required',
      'transaction_id'              => 'nullable:pay_type,card|string|max:255',
      'online_payment_method'       => 'nullable|string|max:255',

      'wallet_used'                   => 'nullable',
      'wallet_amount_used'            => 'nullable',
      
      'points_used'                   => 'nullable',
      'points_amount_used'            => 'nullable',
      'points_amount'                 => 'nullable',
    ];
    return $data;
  }



  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnErrorMessage($validator->errors()->first(),$validator->errors(),['status'=>'fail'],422));
  }
}
