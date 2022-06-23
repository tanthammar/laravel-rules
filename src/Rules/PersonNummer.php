<?php

namespace Tanthammar\LaravelRules\Rules;

use App\Helpers\BookonsHelpers;
use Illuminate\Contracts\Validation\Rule;
use Personnummer\Personnummer as PersonNummerVerifier;
use Personnummer\PersonnummerException;
use TantHammar\LaravelRules\Helpers;

class PersonNummer implements Rule
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
        try {
            $boolean = PersonNummerVerifier::valid(Helpers::clean_numbers($value));
        } catch (\Exception $e) {
            return false;
        }
        return $boolean;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if (app()->getLocale() === 'sv') {
            return 'Du måste ange ett giltigt Personnummer';
        }

        return 'The number must be a valid Swedish identification number';
    }
}
