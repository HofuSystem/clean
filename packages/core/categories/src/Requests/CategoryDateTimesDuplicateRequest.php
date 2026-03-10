<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryDateTimesDuplicateRequest extends FormRequest
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
			 "type"        => ['required','string','in:clothes,sales,services,maid,host'],
			 "date"        => ['required','date'],
			 "category_id" => ['nullable','exists:categories,id'],
			 "city_id"     => ['nullable','exists:cities,id'],
			 "from_date"   => ['required','date','after_or_equal:today'],
			 "to_date"     => ['required','date','after_or_equal:from_date'],
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
			 "type.required"                 =>  trans('type is required'),
			 "type.in"                       =>  trans('type must be receiver or delivery'),
			 "date.required"                 =>  trans('date is required'),
			 "date.date"                     =>  trans('date must be a valid date'),
			 "category_id.required"          =>  trans('category is required'),
			 "category_id.exists"            =>  trans('selected category is not valid'),
			 "city_id.required"              =>  trans('city is required'),
			 "city_id.exists"                =>  trans('selected city is not valid'),
			 "from_date.required"            =>  trans('from date is required'),
			 "from_date.date"                =>  trans('from date must be a valid date'),
			 "from_date.after_or_equal"      =>  trans('from date must be today or later'),
			 "to_date.required"              =>  trans('to date is required'),
			 "to_date.date"                  =>  trans('to date must be a valid date'),
			 "to_date.after_or_equal"        =>  trans('to date must be equal to or after from date'),
		];

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}

