<?php

namespace Core\Orders\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CreateOrderRequest extends FormRequest
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
      'type'                          => 'required|in:clothes,fastorder,sales,services,host,care,selfcare,maidflex,maidscheduled,maidPackage,maidoffer',
      'desc'                          => 'nullable|string',
      'receiving_day'                 => 'nullable|string',
      'receiving_date'                => 'nullable|date|date_format:Y-m-d|after_or_equal:' . now()->format('Y-m-d'),
      'receiving_time'                => 'nullable|date_format:H:i',
      'receiving_to_time'             => 'nullable|date_format:H:i',
      'delivery_day'                  => 'nullable',
      'delivery_date'                 => 'nullable|date|date_format:Y-m-d|after_or_equal:' . now()->format('Y-m-d'),
      'delivery_time'                 => 'nullable|date_format:H:i',
      'delivery_to_time'              => 'nullable|date_format:H:i',

      'service_days_per_week'         => 'nullable|string',
      'service_days_per_month'        => 'nullable|string',
      'execute_day'                   => 'nullable|string',
      'execute_date'                  => 'nullable|date|date_format:Y-m-d|after_or_equal:' . now()->format('Y-m-d'),
      'execute_time'                  => 'nullable|date_format:H:i',
      'execute_to_time'               => 'nullable|date_format:H:i',

      'receiving_lat'                 => 'nullable|string',
      'receiving_lng'                 => 'nullable|string',
      'receiving_location'            => 'nullable|string',

      'delivery_lat'                  => 'nullable|string',
      'delivery_lng'                  => 'nullable|string',
      'delivery_location'             => 'nullable|string',

      'delivery_address_id'          => 'nullable|exists:addresses,id',
      'receiving_address_id'         => 'nullable|exists:addresses,id',
      'execute_address_id'           => 'nullable|exists:addresses,id',

      'coupon_id'                     => 'nullable|exists:coupons,id',
      'total_coupon'                  => 'nullable|required_with:coupon_id',
      'has_coupon'                    => 'nullable',
      'delivery_price'                => 'nullable|numeric',
      'pay_type'                      => 'required|in:card,cash,wallet',
      'transaction_id'                => 'nullable|string|max:255',

      'status'                        => 'nullable|in:pending,pending_payment',

      'order_price'                   => 'required',
      'total_price'                   => 'required',
      'paid'                          => 'nullable',
      'total_cost'                    => 'nullable',

      'wallet_used'                   => 'required',
      'wallet_balance'                => 'required',
      'total_after_wallet'            => 'required',
      'wallet_amount_used'            => 'nullable',
      
      'points_used'                   => 'nullable',
      'points_amount_used'            => 'nullable',
      'points_amount'                 => 'nullable',

      'products'                      => 'nullable|required_if:type,clothes,sales,services',
      'products.*.id'                 => 'nullable|required_if:type,clothes,sales,services|exists:products,id',
      'products.*.quantity'           => 'nullable|required_if:type,clothes,sales,services',
      'products.*.width'              => 'nullable|required_if:products.product_type,carpet',
      'products.*.height'             => 'nullable|required_if:products.product_type,carpet',

      'service_id'                    => 'nullable',
      'service_type_id'               => 'nullable|exists:category_types,id',
      'uniform_id'                    => 'nullable|exists:category_settings,id',
      'worker_count_id'               => 'nullable|exists:category_settings,id',
      'hours_count_id'                => 'nullable|exists:category_settings,id',
      'period_id'                     => 'nullable|exists:category_settings,id',
      'duration_id'                   => 'nullable|exists:category_settings,id',
      'additional_id'                 => 'nullable|exists:category_settings,id',
      'days_per_week'                 => 'nullable',
      'days_per_week_names'           => 'nullable',
      'days_per_month_dates'          => 'nullable',
      'online_payment_method'         => 'nullable',
    ];
    if($this->pay_type == 'card' && $this->status != 'pending_payment'){
      $data['transaction_id'] .= '|required';
    }
    return $data;
  }



  protected function failedValidation(Validator $validator)
  {
    throw new HttpResponseException($this->returnErrorMessage($validator->errors()->first(),$validator->errors(),['status'=>'fail'],422));
  }
}
