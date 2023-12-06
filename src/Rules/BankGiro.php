<?php

namespace TantHammar\LaravelRules\Rules;

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
     */
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        try {
            return (new BankgiroFactory)->createAccount($value)->getBankName() === BankNames::BANK_BANKGIRO;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('laravel-rules::messages.bankgiro');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
