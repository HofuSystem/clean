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
            'city_id'       => 'required_without_all:city_name,lat,lng|exists:cities,id',
            'city_name'     => 'required_without_all:city_id,lat,lng|string',
            'district_id'   => 'required_without_all:district_name,lat,lng|exists:districts,id',
            'district_name' => 'required_without_all:district_id,lat,lng|string',
            'lat'           => 'required_without_all:city_name,district_name,city_id,district_id|numeric',
            'lng'           => 'required_without_all:city_name,district_name,city_id,district_id|numeric',
        ];
    }


}
