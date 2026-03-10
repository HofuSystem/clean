<?php

namespace Core\PhoneNumbers\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\General\Helpers\ToolHelper;
use Core\PhoneNumbers\Helpers\PhoneNumbers;

class PhoneNumberRule implements Rule
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
        if(!is_numeric($value)) {
            $validator->getMessageBag()->add($attribute, trans("Phone should be numeric"));
            return false;
        }
        $data                 = $validator->getData(); 
        $countryKeyAttribute  = $options[0] ?? 'country_key';
        
        $countryCode          = isset($countryKeyAttribute) ? ToolHelper::getDataFromArray($data,$countryKeyAttribute) : null; 
        $res                  = PhoneNumbers::checkPhonNumber($value,$countryCode);

        if(!$res['status']) {
            return $res['status'];
        }

        if(request()->has($attribute)){
            request()->merge([$attribute=>$res['phone']]);
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
