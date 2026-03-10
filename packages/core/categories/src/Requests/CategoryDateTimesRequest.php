<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryDateTimesRequest extends FormRequest
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
		 "new_type"                    => ['nullable'],
		 "new_category_id"             => ['nullable','exists:categories,id'],
		 "new_city_id"                 => ['nullable','exists:cities,id'],
		 "new_date"                    => ['nullable','date','after_or_equal:today'],
       "times"                       => ['required','array'], 
       "times.*.from"                => ["required"],
       "times.*.to"                  => ["required"],
       "times.*.order_count"         => ["required","numeric","min:1"],
       "times.*.receiver_count"      => ["nullable","numeric","min:0"],
       "times.*.delivery_count"      => ["nullable","numeric","min:0"],
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
			 "type.in"                       =>  trans('type is not allowed'),
			 "type.required"                 =>  trans('type is required'),
			 "category_id.exists"            =>  trans('category is not Valid'),
			 "category_id.required"          =>  trans('category is required'),
			 "city_id.exists"                =>  trans('city is not Valid'),
			 "city_id.required"              =>  trans('city is required'),
			 "date.date"                     =>  trans('date should be a date'),
			 "date.required"                 =>  trans('date is required'),
			 "from.time"                     =>  trans('from should be a time'),
			 "from.required"                 =>  trans('from is required'),
			 "to.time"                       =>  trans('to should be a time'),
			 "to.required"                   =>  trans('to is required'),
			 "order_count.numeric"           =>  trans('order count should be a number'),
			 "order_count.required"          =>  trans('order count is required'),
			 "receiver_count.numeric"        =>  trans('receiver count should be a number'),
			 "delivery_count.numeric"        =>  trans('delivery count should be a number'),
			];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
