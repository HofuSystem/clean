<?php

namespace Core\Wallet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class WalletTransactionsRequest extends FormRequest
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
			 "type"            =>  ['required','in:deposit,withdraw'], 
			 "amount"          =>  ['required','numeric'], 
			 "transaction_id"  =>  ['nullable','string'], 
			 "bank_name"       =>  ['nullable','string'], 
			 "account_number"  =>  ['nullable','string'], 
			 "iban_number"     =>  ['nullable','string'], 
			 "user_id"         =>  ['nullable','exists:users,id'], 
			 "added_by_id"     =>  ['nullable','exists:users,id'], 
			 "package_id"      =>  ['nullable','exists:wallet_packages,id'], 
			 "expired_at"      =>  ['nullable','date'], 
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
			 "type.in"                  =>  trans('type is not allowed'), 
			 "type.required"            =>  trans('type is required'), 
			 "amount.numeric"           =>  trans('amount should be a number'), 
			 "amount.required"          =>  trans('amount is required'), 
			 "wallet_before.numeric"    =>  trans('wallet before should be a number'), 
			 "wallet_before.required"   =>  trans('wallet before is required'), 
			 "wallet_after.numeric"     =>  trans('wallet after should be a number'), 
			 "wallet_after.required"    =>  trans('wallet after is required'), 
			 "status.in"                =>  trans('status is not allowed'), 
			 "status.required"          =>  trans('status is required'), 
			 "transaction_id.string"    =>  trans('transaction id should be a string'), 
			 "transaction_id.required"  =>  trans('transaction id is required'), 
			 "bank_name.string"         =>  trans('bank name should be a string'), 
			 "bank_name.required"       =>  trans('bank name is required'), 
			 "account_number.string"    =>  trans('account number should be a string'), 
			 "account_number.required"  =>  trans('account number is required'), 
			 "iban_number.string"       =>  trans('iban number should be a string'), 
			 "iban_number.required"     =>  trans('iban number is required'), 
			 "user_id.exists"           =>  trans('user is not Valid'), 
			 "user_id.required"         =>  trans('user is required'), 
			 "added_by_id.exists"       =>  trans('added by is not Valid'), 
			 "added_by_id.required"     =>  trans('added by is required'), 
			 "package_id.exists"        =>  trans('package is not Valid'), 
			 "package_id.required"      =>  trans('package is required'), 
			 "expired_at.date"          =>  trans('expired at should be a date'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
