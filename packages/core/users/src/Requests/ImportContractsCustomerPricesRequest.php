<?php

namespace Core\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportContractsCustomerPricesRequest extends FormRequest
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
		 "data"                      =>  ['required','array'], 
		 "data.*.contract_id"        =>  ['required','exists:contracts,id'], 
		 "data.*.product_id"         =>  ['nullable','exists:products,id'], 
		 "data.*.over_price"         =>  ['required','numeric'], 
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
		 "data.required"                      =>  trans('data is required'), 
		 "data.array"                         =>  trans('data should be array'), 
		 "data.*.contract_id.exists"          =>  trans('contract is not Valid'), 
		 "data.*.contract_id.required"        =>  trans('contract is required'), 
		 "data.*.product_id.exists"           =>  trans('product is not Valid'), 
		 "data.*.product_id.required"         =>  trans('product is required'), 
		 "data.*.over_price.numeric"          =>  trans('over price should be a number'), 
		 "data.*.over_price.required"         =>  trans('over price is required'), 
		]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

