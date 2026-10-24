<?php

namespace Core\Wallet\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class ChargeWalletRequest extends FormRequest
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
            'transaction_id' => 'required|string|between:2,250',
            'analytics_context' => 'nullable|array:app_instance_id,app_version,session_id,platform',
            'analytics_context.app_instance_id' => 'nullable|string|max:255',
            'analytics_context.app_version' => 'nullable|string|max:100',
            'analytics_context.session_id' => 'nullable|integer|min:0',
            'analytics_context.platform' => 'nullable|string|in:ios,android',
        ];
    }
}
