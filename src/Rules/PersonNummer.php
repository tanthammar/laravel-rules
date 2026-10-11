<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Personnummer\Personnummer as PersonNummerVerifier;
use TantHammar\LaravelExtras\CleanNumber;

class PersonNummer implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return PersonNummerVerifier::valid(CleanNumber::make($value)); //catches errors
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('laravel-rules::messages.person-nr');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
