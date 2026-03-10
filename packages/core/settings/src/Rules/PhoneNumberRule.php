<?php

namespace Core\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\General\Helpers\Settings\PhoneNumbers;

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

        if(isset($value)){
            $res = PhoneNumbers::checkPhonNumber($value);
            if(!$res['status'])
                return $res['status'];

            if(!empty($options)){
                $data = $validator->getData();
                if(!isset($data[$options[0]])){
                    $validator->getMessageBag()->add($attribute, "the $options[0] is required to check");
                    return false;
                }
                if($res['phone']->getCountryCode() != $data[$options[0]]){
                    $validator->getMessageBag()->add($options[0], "the Country code doesn't match this phone number");
                    return false;
                }
                $data[$attribute] =  $res['phone']->getNationalNumber();
                $validator->setData($data);
            }
            if(request()->has($attribute)){
                request()->merge([$attribute=>$res['phone']->getNationalNumber()]);

            }
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
