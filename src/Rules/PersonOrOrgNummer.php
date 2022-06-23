<?php

namespace Tanthammar\LaravelRules\Rules;

use App\Helpers\BookonsHelpers;
use Illuminate\Contracts\Validation\Rule;
use Intervention\Validation\Validator;
use Intervention\Validation\Exception\ValidationException;
use Personnummer\Personnummer as PersonNummerVerifier;
use Personnummer\PersonnummerException;
use TantHammar\LaravelRules\Helpers;

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
