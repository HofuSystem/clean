<?php

namespace Core\Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Core\General\Helpers\ToolHelper;

class JsonArray implements Rule
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
       $res = ToolHelper::isJson($value) ? json_decode($value,true) : null;
       request()->merge([$attribute => $res]);
       return is_array($res);
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
