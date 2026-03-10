<?php

namespace Core\MediaCenter\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\General\Helpers\Settings\RmMainSettings;

class MediaCentersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'url'=>'required|file',
            'title'=>'required|max:191',
            'alt'=>'required|max:191',
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
