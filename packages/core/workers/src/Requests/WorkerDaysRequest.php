<?php

namespace Core\Workers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class WorkerDaysRequest extends FormRequest
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
			 "worker_id"  =>  ['required','exists:workers,id'], 
			 "date"       =>  ['required','date'], 
			 "type"       =>  ['required','in:absence,attendees'], 
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
			 "worker_id.exists"    =>  trans('worker is not Valid'), 
			 "worker_id.required"  =>  trans('worker is required'), 
			 "date.date"           =>  trans('date should be a date'), 
			 "date.required"       =>  trans('date is required'), 
			 "type.in"             =>  trans('type is not allowed'), 
			 "type.required"       =>  trans('type is required'), 
			]; 

    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
