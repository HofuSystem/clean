<?php

namespace Core\PhoneNumbers\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\PhoneNumbers\Helpers\PhoneNumbers;

class PhoneUniqueRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {


    }
    public function validate($attribute, $value,$options,$validator)
    {
        if(!isset($value)) return true;
        
       
        
        $data                   = $validator->getData(); 
        $countryKeyAttribute    = $options[1] ?? "country_key";
        $id                     = $options[2] ?? null;
        $res                    = PhoneNumbers::checkPhonNumber($value,$data[$countryKeyAttribute]);
        if(!$res['status']) {
            return $res['status'];
        }
        // dd("ASDF");
        

        return \DB::table($options[0])
            ->when(isset($id) and !empty($id),function($idQuery)use($id){
                $idQuery->where('id','!=',$id);
            })
            ->where($attribute,$res['phone'])
            ->where($countryKeyAttribute,$res['code'])
            ->doesntExist();    
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('this phone number is already Taken');
    }
}
