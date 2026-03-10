<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderReportRequest extends FormRequest
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
            'type'              => 'required|in:report,location',
            'report_reason_id'  => 'nullable|required_if:type,report',
            'desc_location'     => 'nullable|required_if:type,location',
        ];
    }
}
