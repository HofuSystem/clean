<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam phone string required
 * @bodyParam code string required
 */
class CodeRequest extends FormRequest
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
            'code' => 'required|numeric',
            'phone' => 'required|numeric',
            'device_token' => 'nullable',
            'type' => 'nullable|in:ios,android',
        ];
    }


}
