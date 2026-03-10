<?php

namespace Core\Coupons\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class FindCouponsRequest extends FormRequest
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
			"code"                   =>  ['required', 'string'],
			"order_type"             =>  ['required'],
			"products_ids"           =>  ['nullable', 'array'],
			"products_ids.*"         =>  ['exists:products,id'],
			"order_total"            =>  ['required', 'numeric'],
		];
	}



	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException($this->returnValidationError($validator));
	}
}
