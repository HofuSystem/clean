<?php

namespace Core\Users\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;


class EditProfileRequest extends FormRequest
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
        $user = auth()->user()->id;

        return [
           'fullname'           => 'required|string|between:3,250',
           'image'              => 'nullable|image|mimes:jpg,jpeg,png',
           'email'              => 'nullable|email|unique:users,email,'.$user,
           'date_of_birth'      => 'nullable',
           'gender'             => 'nullable|in:male,female',
           'city_id'            => 'nullable|exists:cities,id,deleted_at,NULL',
           'district_id'        => 'nullable|exists:districts,id,deleted_at,NULL',
           'other_city_name'    => 'nullable',
           'lat'                => 'nullable',
           'lng'                => 'nullable',
        ];
    }


}
