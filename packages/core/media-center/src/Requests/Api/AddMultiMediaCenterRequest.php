<?php

namespace Core\MediaCenter\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\General\Helpers\Settings\RmMainSettings;

class AddMultiMediaCenterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'url.*' => [
                'required',
                'file',
//                'mimes:jpg,jpeg,png,webp,mp4,webm',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'error',
            'data' => $validator->errors()
        ], 200));
    }
}
