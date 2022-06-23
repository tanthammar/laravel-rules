<?php

namespace Tanthammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Intervention\Validation\Rules\Luhn;
use TantHammar\LaravelRules\Helpers;

class OrgNummer implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        try {
            $boolean = (new Luhn)->passes(attribute: null, value: Helpers::clean_numbers($value));
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
            return 'Organisationsnumret är ogiltigt';
        }

        return 'The number must be a valid business ID';
    }
}
