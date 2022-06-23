<?php

namespace Tanthammar\LaravelRules\Rules;

use byrokrat\banking\BankgiroFactory;
use byrokrat\banking\BankNames;
use byrokrat\banking\Exception;
use Illuminate\Contracts\Validation\Rule;

class BankGiro implements Rule
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
            return (new BankgiroFactory)->createAccount($value)->getBankName() === BankNames::BANK_BANKGIRO;
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
        if(app()->getLocale() === 'sv') {
            return 'Du måste ange ett giltigt BankGiro nummer';
        }

        return 'The number must be a valid Swedish BankGiro';
    }
}
