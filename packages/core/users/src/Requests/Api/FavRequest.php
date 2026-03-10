<?php

namespace Core\Users\Requests\Api;


use Illuminate\Foundation\Http\FormRequest;

class FavRequest extends FormRequest
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
            'type'              => 'required|in:product,category',
            'product_id'        => 'nullable|required_if:type,product|exists:products,id',
            'category_id'       => 'nullable|required_if:type,category|exists:categories,id',
        ];
    }
}
