<?php

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Mpociot\VatCalculator\Facades\VatCalculator;

/**
 * This never calls an external api, only does regex comparison
 */
class VatNumberFormat implements Rule
{

    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return VatCalculator::isValidVatNumberFormat($value);

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
