<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;
use Mpociot\VatCalculator\VatCalculator;
use TantHammar\LaravelRules\Services\EuVatPrefixes;

/**
 * This calls an VIES api falls back to number format check if service is unavailable
 * https://ec.europa.eu/taxation_customs/vies/#/self-monitoring
 */
class VatNumberAPIWithFormatFallback implements Rule
{
    /**
     * Shortest EU vat number is 10 chars, so this only rejects the obviously too short.
     */
    public const MIN_LENGTH = 8;

    protected static function normalize(string $vatNumber): string
    {
        return str_replace([' ', "\xC2\xA0", "\xA0", '-', '.', ','], '', trim($vatNumber));
    }

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

        $prefix = EuVatPrefixes::of($value);

        if (! ctype_alpha($prefix)) {
            return false;
        }

        // Do not use Facade. Configure VatCalculator to throw an error when country != GB, else only bool false is returned
        $calculator = new VatCalculator(['forward_soap_faults' => true]);

        if (! EuVatPrefixes::contains($prefix)) {
            return EuVatPrefixes::hasFormatPattern($prefix)
                ? $calculator->isValidVatNumberFormat($value)
                : strlen(self::normalize($value)) >= self::MIN_LENGTH;
        }

        try {
            // This check validates via external api, trows error if service is unavailable
            return $calculator->isValidVATNumber($value);

        } catch (VATCheckUnavailableException) {

            // use fallback if service is unavailable, this only checks the format
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

    // Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
