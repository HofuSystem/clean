<?php

namespace Core\Categories\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Core\Settings\Traits\ApiResponse;

class CategoryDateTimesCreateRequest extends FormRequest
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
			  "type"                      =>  ['required','in:clothes,services,sales,maid,host'], 
			  "category_id"               =>  ['nullable','exists:categories,id'], 
			  "city_id"                   =>  ['nullable','exists:cities,id'], 
			  "date_from"                 =>  ['required','date','before:date_to'], 
			  "date_to"                   =>  ['required','date'], 
			  "of_days"                   =>  ['array'], 
			  "of_days.*"                 =>  ['date'], 
			  "weekends"                  =>  ['array'], 
			  "weekends.*"                =>  ['in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'], 
			  "times"                     =>  ['required','array'], 
        "times.*.from_time"         => ["required","date_format:H:i"],
        "times.*.to_time"           => ["required","date_format:H:i"],
        "times.*.receiver_count"    => ["required","numeric","min:0"],
        "times.*.delivery_count"    => ["required","numeric","min:0"],
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
        // Date From
        'date_from.required'  => trans('The start date is required.'),
        'date_from.date'      => trans('The start date must be a valid date.'),
        'date_from.before'    => trans('The start date must be before the end date.'),
    
        // Date To
        'date_to.required'  => trans('The end date is required.'),
        'date_to.date'      => trans('The end date must be a valid date.'),
    
        // Off Days
        'off_days.array'  => trans('The off days must be provided as an array.'),
        'off_days.*.date' => trans('Each off day must be a valid date.'),
    
        // Weekends
        'weekends.array'  => trans('The weekends must be provided as an array.'),
        'weekends.*.in'   => trans('Each weekend day must be a valid day of the week (e.g., Monday, Tuesday).'),
    
        // Times
        'times.required'  => trans('At least one time slot is required.'),
        'times.array'     => trans('The times must be provided as an array.'),
    
        // From Time
        'times.*.from_time.required'  => trans('The start time is required for each time slot.'),
        'times.*.from_time.date_format'      => trans('The start time must be in the format HH:MM.'),
    
        // To Time
        'times.*.to_time.required'  => trans('The end time is required for each time slot.'),
        'times.*.to_time.date_format'      => trans('The end time must be in the format HH:MM.'),
    
        // Order Count
        'times.*.order_count.required'  => trans('The order count is required for each time slot.'),
        'times.*.order_count.numeric'   => trans('The order count is required for each time slot.'),
        'times.*.order_count.min'       => trans('The order count must be at least 1.'),
    
        // Receiver Count
        'times.*.receiver_count.numeric'  => trans('The receiver count must be a number.'),
        'times.*.receiver_count.min'      => trans('The receiver count must be at least 0.'),
    
        // Delivery Count
        'times.*.delivery_count.numeric'  => trans('The delivery count must be a number.'),
        'times.*.delivery_count.min'      => trans('The delivery count must be at least 0.'),
    ];
    }

    protected function failedValidation(Validator $validator)
    {
      throw new HttpResponseException($this->returnValidationError($validator));
    }

}
