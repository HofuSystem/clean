<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ContractPriceUpdateRequest extends FormRequest
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
        // Get the contract price ID from route
        $priceId = $this->route('id');
        
        // Get the contract price and its max allowed value
        $contractPrice = \Core\Users\Models\ContractsPrice::find($priceId);
        $maxAllowed = $contractPrice?->contract?->max_allowed_over_price;
        
        $rules = [
            'over_price' => ['required', 'numeric', 'min:0'],
        ];
        
        // Add max validation if max_allowed_over_price is set in the contract
        if ($maxAllowed !== null && $maxAllowed > 0) {
            $rules['over_price'][] = 'max:' . $maxAllowed;
        }
        
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        // Get the contract price ID from route
        $priceId = $this->route('id');
        
        // Get the contract price and its max allowed value
        $contractPrice = \Core\Users\Models\ContractsPrice::find($priceId);
        $maxAllowed = $contractPrice?->contract?->max_allowed_over_price;
        
        return [
            'over_price.required' => trans('client.over_price_required'),
            'over_price.numeric' => trans('client.over_price_numeric'),
            'over_price.min' => trans('client.over_price_min'),
            'over_price.max' => trans('client.over_price_max', ['max' => $maxAllowed]),
        ];
    }
}

