<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ContractsCustomerPricesRequest extends FormRequest
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
		 "contract_id"  =>  ['required','exists:contracts,id'], 
		 "product_id"   =>  ['nullable','exists:products,id'], 
		 "over_price"   =>  ['required','numeric'], 
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
		 "contract_id.exists"    =>  trans('contract is not Valid'), 
		 "contract_id.required"  =>  trans('contract is required'), 
		 "product_id.exists"     =>  trans('product is not Valid'), 
		 "product_id.required"   =>  trans('product is required'), 
		 "over_price.numeric"    =>  trans('over price should be a number'), 
		 "over_price.required"   =>  trans('over price is required'), 
		]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

