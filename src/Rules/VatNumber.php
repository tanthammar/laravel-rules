<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;
use Mpociot\VatCalculator\Facades\VatCalculator;

class VatNumber implements Rule
{
    protected bool $serviceUnavailable = false;

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
            return VatCalculator::isValidVATNumber($value);
        } catch (VATCheckUnavailableException) {
            $this->serviceUnavailable = true;

            return false;
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->serviceUnavailable ? trans('laravel-rules::messages.vat-service-unavailable') : trans('laravel-rules::messages.vat-invalid');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
