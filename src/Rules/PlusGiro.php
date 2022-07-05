<?php

namespace App\Rules;

use byrokrat\banking\BankNames;
use byrokrat\banking\Exception;
use byrokrat\banking\PlusgiroFactory;
use Illuminate\Contracts\Validation\Rule;

class PlusGiro implements Rule
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
            return (new PlusgiroFactory)->createAccount($value)->getBankName() === BankNames::BANK_PLUSGIRO;
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
        return __('laravel-rules::messages.plusgiro');
    }
}
