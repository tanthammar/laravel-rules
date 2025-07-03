<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;

/**
 * This calls an VIES api falls back to number format check if service is unavailable
 * https://ec.europa.eu/taxation_customs/vies/#/self-monitoring
 */
class VatNumberAPIWithFormatFallback implements Rule
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

        // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
        $calculator = new \Mpociot\VatCalculator\VatCalculator(['forward_soap_faults' => true]);

        try {
            //This check validates via external api, trows error if service is unavailable
            return $calculator->isValidVATNumber($value);

        } catch (VATCheckUnavailableException) {

            //use fallback if service is unavailable, this only checks the format
            return $calculator->isValidVatNumberFormat($value);
        }

    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('laravel-rules::messages.vat-invalid');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
