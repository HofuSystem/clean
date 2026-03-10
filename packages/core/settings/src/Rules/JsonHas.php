<?php

namespace Core\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\General\Helpers\ToolHelper;

class JsonHas implements Rule
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
        $value = ToolHelper::isJson($value) ? json_decode($value,true) : $value;
        return (is_array($value) and isset($value[$options[0]]) and !empty($value[$options[0]]));
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
