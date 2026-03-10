<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'items'            =>  'required|array|min:1',
            'items.*.id'       =>  'required|numeric|exists:products,id',
            'items.*.quantity' =>  'required|numeric',
            'items.*.width'    =>  'nullable|numeric',
            'items.*.height'   =>  'nullable|numeric'
        ];
    }
}
