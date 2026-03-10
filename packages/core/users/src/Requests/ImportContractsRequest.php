<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportContractsRequest extends FormRequest
{
    use ApiResponse;

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
			 "data.*.title"                        =>  ['required','string'], 
			 "data.*.months_count"                =>  ['required','numeric'], 
			 "data.*.month_fees"                   =>  ['nullable','numeric'], 
			 "data.*.start_date"                   =>  ['nullable','date'], 
			 "data.*.end_date"                     =>  ['nullable','date'], 
			 "data.*.client_id"                    =>  ['nullable','exists:users,id'], 
			 "data.*.contractPrices"               =>  ['nullable','array'], 
			 "data.*.contractPrices.*.product_id"  =>  ['nullable','exists:products,id'], 
			 "data.*.contractPrices.*.price"       =>  ['required','numeric'], 
			]; 

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
      return [ 
			 "data.*.title.string"                           =>  trans('title should be a string'), 
			 "data.*.title.required"                         =>  trans('title is required'), 
			 "data.*.months_count.numeric"                  =>  trans('months count should be a number'), 
			 "data.*.months_count.required"                 =>  trans('months count is required'), 
			 "data.*.month_fees.numeric"                     =>  trans('month fees should be a number'), 
			 "data.*.month_fees.required"                    =>  trans('month fees is required'), 
			 "data.*.start_date.date"                        =>  trans('start date should be a date'), 
			 "data.*.start_date.required"                    =>  trans('start date is required'), 
			 "data.*.end_date.date"                          =>  trans('end date should be a date'), 
			 "data.*.end_date.required"                      =>  trans('end date is required'), 
			 "data.*.client_id.exists"                       =>  trans('client is not Valid'), 
			 "data.*.client_id.required"                     =>  trans('client is required'), 
			 "data.*.contractPrices.array"                   =>  trans('contract prices is not array'), 
			 "data.*.contractPrices.*.contract_id.exists"    =>  trans('contract is not Valid'), 
			 "data.*.contractPrices.*.contract_id.required"  =>  trans('contract is required'), 
			 "data.*.contractPrices.*.product_id.exists"     =>  trans('product is not Valid'), 
			 "data.*.contractPrices.*.product_id.required"   =>  trans('product is required'), 
			 "data.*.contractPrices.*.price.numeric"         =>  trans('price should be a number'), 
			 "data.*.contractPrices.*.price.required"        =>  trans('price is required'), 
			 "data.*.contractPrices.required"                =>  trans('contract prices is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
