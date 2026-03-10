<?php

namespace Core\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportWalletTransactionsRequest extends FormRequest
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
			 "data.*.type"            =>  ['required','in:deposit,withdraw'], 
			 "data.*.amount"          =>  ['required','numeric'], 
			 "data.*.wallet_before"   =>  ['required','numeric'], 
			 "data.*.wallet_after"    =>  ['required','numeric'], 
			 "data.*.status"          =>  ['required','in:accepted,rejected'], 
			 "data.*.transaction_id"  =>  ['nullable','string'], 
			 "data.*.bank_name"       =>  ['nullable','string'], 
			 "data.*.account_number"  =>  ['nullable','string'], 
			 "data.*.iban_number"     =>  ['nullable','string'], 
			 "data.*.user_id"         =>  ['nullable','exists:users,id'], 
			 "data.*.added_by_id"     =>  ['nullable','exists:users,id'], 
			 "data.*.package_id"      =>  ['nullable','exists:wallet_packages,id'], 
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
			 "data.*.type.in"                  =>  trans('type is not allowed'), 
			 "data.*.type.required"            =>  trans('type is required'), 
			 "data.*.amount.numeric"           =>  trans('amount should be a number'), 
			 "data.*.amount.required"          =>  trans('amount is required'), 
			 "data.*.wallet_before.numeric"    =>  trans('wallet before should be a number'), 
			 "data.*.wallet_before.required"   =>  trans('wallet before is required'), 
			 "data.*.wallet_after.numeric"     =>  trans('wallet after should be a number'), 
			 "data.*.wallet_after.required"    =>  trans('wallet after is required'), 
			 "data.*.status.in"                =>  trans('status is not allowed'), 
			 "data.*.status.required"          =>  trans('status is required'), 
			 "data.*.transaction_id.string"    =>  trans('transaction id should be a string'), 
			 "data.*.transaction_id.required"  =>  trans('transaction id is required'), 
			 "data.*.bank_name.string"         =>  trans('bank name should be a string'), 
			 "data.*.bank_name.required"       =>  trans('bank name is required'), 
			 "data.*.account_number.string"    =>  trans('account number should be a string'), 
			 "data.*.account_number.required"  =>  trans('account number is required'), 
			 "data.*.iban_number.string"       =>  trans('iban number should be a string'), 
			 "data.*.iban_number.required"     =>  trans('iban number is required'), 
			 "data.*.user_id.exists"           =>  trans('user is not Valid'), 
			 "data.*.user_id.required"         =>  trans('user is required'), 
			 "data.*.added_by_id.exists"       =>  trans('added by is not Valid'), 
			 "data.*.added_by_id.required"     =>  trans('added by is required'), 
			 "data.*.package_id.exists"        =>  trans('package is not Valid'), 
			 "data.*.package_id.required"      =>  trans('package is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
