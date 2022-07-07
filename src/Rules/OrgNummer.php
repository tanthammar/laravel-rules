<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Intervention\Validation\Rules\Luhn;
use TantHammar\LaravelExtras\LaravelExtrasHelpers;

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
        if(blank($value)) {
            return false;
        }
        try {
            return (new Luhn)->passes(attribute: null, value: LaravelExtrasHelpers::clean_numbers($value));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('laravel-rules::messages.org-nr');
    }
}
