<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;


class ReferralUpdateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->has('referral_code')) {
            $this->merge([
                'referral_code' => trim($this->referral_code),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'referral_code'      => 'required|string|exists:users,referral_code',

        ];
    }
    public function messages()
    {
        return [
            'referral_code.required' => trans('referral code is required'),
            'referral_code.exists'   => trans('code is not valid'),
        ];
    }

}
