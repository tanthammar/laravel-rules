<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Organisationsnummer\Organisationsnummer;
use TantHammar\LaravelExtras\NoWhiteSpace;

/** Validates SWEDISH business numbers */
class OrgNummer implements Rule
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
        if (blank($value)) {
            return false;
        }

        return Organisationsnummer::valid(NoWhiteSpace::make($value)); //only returns bool (catches errors)
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

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
