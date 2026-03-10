<?php

namespace Core\Settings\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryChargesRequest extends FormRequest
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
            'delivery_charge'   =>  'required|numeric|min:0',
            'free_delivery_min' =>  'required|numeric|min:0',
           
        ];
    }
}
