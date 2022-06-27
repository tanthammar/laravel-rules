<?php

namespace Tanthammar\LaravelRules\Rules;

use byrokrat\banking\AccountFactory;
use byrokrat\banking\Exception;
use Illuminate\Contracts\Validation\Rule;

class BankKonto implements Rule
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
        if(blank($value)) {
            return false;
        }
        try {
            return filled((new AccountFactory)->createAccount($value));
        } catch (Exception $e) {
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
        if (app()->getLocale() === 'sv') {
            return 'Du måste ange ett giltigt svenskt Bankkonto';
        }

        return 'The number must be a valid Swedish Bank Account';
    }
}
