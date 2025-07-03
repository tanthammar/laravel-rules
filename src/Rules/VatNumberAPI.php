<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;

/**
 * This calls an VIES api and throws error if service is unavailable
 * WARNING
 * Service may be unavailable for hours or days which would return incorrect validation status
 * If your form is prevented to be submitted if there are validation errors, your form may malfunction when service is down
 * Check server status for all countries https://ec.europa.eu/taxation_customs/vies/#/self-monitoring
 */
class VatNumberAPI implements Rule
{
    protected bool $serviceUnavailable = false;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @throws VATCheckUnavailableException
     */
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
        $calculator = new \Mpociot\VatCalculator\VatCalculator(['forward_soap_faults' => true]);

        //This check validates via external api, throws error if service is unavailable
        return $calculator->isValidVATNumber($value);

    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return trans('laravel-rules::messages.vat-invalid');
    }

    //Laravel 10

    /**
     * @throws VATCheckUnavailableException
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
