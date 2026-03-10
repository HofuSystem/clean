<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProviderLoginRequest extends FormRequest
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
            'identifier'    => 'required',
            'password'      => 'required',
            'type'          => 'nullable|in:ios,android,huawei',
            'device_token'  => 'nullable',
        ];
    }
}
