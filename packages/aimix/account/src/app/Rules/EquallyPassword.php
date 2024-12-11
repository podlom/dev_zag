<?php

namespace Aimix\Account\app\Rules;

use Illuminate\Contracts\Validation\Rule;

class EquallyPassword implements Rule
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
        $oldPass = \Auth::user()->password;
        
        return \Hash::check($value, $oldPass);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Пароль введен неверно.';
    }
}
