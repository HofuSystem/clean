<?php

namespace Core\MediaCenter\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Core\General\Helpers\Settings\RmMainSettings;

class AddMediaCenterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
//        $set = RmMainSettings::get();
//        $types = $set['media_center_settings']['available_types'];
        return [
            'url' => [
                'required',
                'file',
            ],
        ];
    }
}
