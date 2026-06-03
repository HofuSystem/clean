<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
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
            'city_id'     => 'nullable|required_without_all:lat,lng|exists:cities,id',
            'district_id' => 'nullable|required_without_all:lat,lng|exists:districts,id',
            'lat'         => 'nullable|required_without_all:city_id,district_id|numeric',
            'lng'         => 'nullable|required_without_all:city_id,district_id|numeric',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasCityAndDistrict = ($this->filled('city_id') || $this->filled('city')) && 
                                  ($this->filled('district_id') || $this->filled('district'));
            $hasLatLng = ($this->filled('lat') || $this->filled('latitude')) && 
                         ($this->filled('lng') || $this->filled('longitude'));

            if (!$hasCityAndDistrict && !$hasLatLng) {
                $validator->errors()->add('location', trans('Either city and district or coordinates are required'));
            }
        });
    }
}
