<?php

namespace Core\Users\Requests\Api;

use Core\Settings\Services\SettingsService;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
        $rules =  [
          'phone'           =>['required','regex:/^(05|5)[5|0|3|6|4|9|1|8|7][0-9]{7}$/'],
          'device_token'    => 'nullable',
          'type'            => 'nullable|in:ios,android,huawei',
        ];
        $loginMethod = SettingsService::getDataBaseSetting('login_using');
        if($loginMethod == 'password'){
            $rules['password'] = ['required'];
        }
        return $rules;
    }


}
