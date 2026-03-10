<?php

namespace Core\Wallet\Requests\Api;



use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
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
            'amount'=> 'required|integer|gte:1',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'iban_number' => 'required|string',
        ];
    }
}
