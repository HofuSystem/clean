<?php

namespace Core\Orders\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
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
            'status'                => 'required|in:pending,failed_payment,pending_payment,cancel_payment',
            'transaction_id'        => 'nullable|string|max:255',
            'online_payment_method' => 'nullable|string|max:255',
        ];
    }
}
