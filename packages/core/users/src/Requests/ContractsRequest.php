<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ContractsRequest extends FormRequest
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
			"title" => ['required', 'string'],
			"months_count" => ['required', 'numeric'],
			"month_fees" => ['nullable', 'numeric'],
			"max_allowed_over_price" => ['nullable', 'numeric', 'min:0'],
			"unlimited_days" => ['nullable', 'boolean'],
			"number_of_days" => ['nullable', 'numeric'],
			"start_date" => ['nullable', 'date'],
			"end_date" => ['nullable', 'date'],
			"client_id" => ['nullable', 'exists:users,id'],
			"contractPrices" => ['nullable', 'array'],
			"contractPrices.*.product_id" => ['nullable', 'exists:products,id'],
			"contractPrices.*.price" => ['required', 'numeric'],
			"contractCustomerPrices" => ['nullable', 'array'],
			"contractCustomerPrices.*.product_id" => ['nullable', 'exists:products,id'],
			"contractCustomerPrices.*.over_price" => ['required', 'numeric'],
			"commercial_registration" => ['nullable', 'string'],
			"tax_number" => ['nullable', 'string'],
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
			"title.string" => trans('title should be a string'),
			"title.required" => trans('title is required'),
			"months_count.numeric" => trans('months count should be a number'),
			"months_count.required" => trans('months count is required'),
			"month_fees.numeric" => trans('month fees should be a number'),
			"month_fees.required" => trans('month fees is required'),
			"max_allowed_over_price.numeric" => trans('max allowed over price should be a number'),
			"max_allowed_over_price.min" => trans('max allowed over price must be at least 0'),
			"unlimited_days.boolean" => trans('unlimited days should be a boolean'),
			"number_of_days.numeric" => trans('number of days should be a number'),
			"start_date.date" => trans('start date should be a date'),
			"start_date.required" => trans('start date is required'),
			"end_date.date" => trans('end date should be a date'),
			"end_date.required" => trans('end date is required'),
			"client_id.exists" => trans('client is not Valid'),
			"client_id.required" => trans('client is required'),
			"contractPrices.array" => trans('contract prices is not array'),
			"contractPrices.*.contract_id.exists" => trans('contract is not Valid'),
			"contractPrices.*.contract_id.required" => trans('contract is required'),
			"contractPrices.*.product_id.exists" => trans('product is not Valid'),
			"contractPrices.*.product_id.required" => trans('product is required'),
			"contractPrices.*.price.numeric" => trans('price should be a number'),
			"contractPrices.*.price.required" => trans('price is required'),
			"contractPrices.required" => trans('contract prices is required'),
			"contractCustomerPrices.array" => trans('contract customer prices is not array'),
			"contractCustomerPrices.*.product_id.exists" => trans('product is not Valid'),
			"contractCustomerPrices.*.product_id.required" => trans('product is required'),
			"contractCustomerPrices.*.over_price.numeric" => trans('over price should be a number'),
			"contractCustomerPrices.*.over_price.required" => trans('over price is required'),
			"contractCustomerPrices.required" => trans('contract customer prices is required'),
		];

	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException($this->returnValidationError($validator));
	}

}
