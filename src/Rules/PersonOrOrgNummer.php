<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;

class PersonOrOrgNummer implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return (new OrgNummer)->passes(null, $value) || (new PersonNummer)->passes(null, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if (app()->getLocale() === 'sv') {
            return 'Numret är ogiltigt';
        }

        return 'The number is invalid';
    }
}
