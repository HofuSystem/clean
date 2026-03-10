<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class UsersUpdatePasswordRequest extends FormRequest
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
			"password" =>  ['required', 'string', 'min:6'],
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
			"password.string"  	=>  trans('password should be a string'),
			"password.required"	=>  trans('password is required'),
			"password.min" 		=>  trans('password is short'),

		];
	}

	protected function failedValidation(Validator $validator)
	{
		throw new HttpResponseException($this->returnValidationError($validator));
	}
}
