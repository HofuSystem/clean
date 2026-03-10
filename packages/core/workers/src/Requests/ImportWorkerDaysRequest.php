<?php

namespace Core\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class ImportWorkerDaysRequest extends FormRequest
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
			 "data.*.worker_id"  =>  ['required','exists:workers,id'], 
			 "data.*.date"       =>  ['required','date'], 
			 "data.*.type"       =>  ['required','in:absence,attendees'], 
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
			 "data.*.worker_id.exists"    =>  trans('worker is not Valid'), 
			 "data.*.worker_id.required"  =>  trans('worker is required'), 
			 "data.*.date.date"           =>  trans('date should be a date'), 
			 "data.*.date.required"       =>  trans('date is required'), 
			 "data.*.type.in"             =>  trans('type is not allowed'), 
			 "data.*.type.required"       =>  trans('type is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
