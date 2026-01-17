<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class FinnishBusinessId implements Rule
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

        return $this->isValidFinnishBusinessId($value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('laravel-rules::messages.finnish-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Validate Finnish Y-tunnus (Business ID).
     */
    private function isValidFinnishBusinessId(string $ytunnus): bool
    {
        // Remove whitespace
        $ytunnus = trim($ytunnus);

        // Remove FI prefix if present (case insensitive)
        if (stripos($ytunnus, 'FI') === 0) {
            $ytunnus = substr($ytunnus, 2);
        }

        // Remove hyphens and spaces
        $ytunnus = str_replace([' ', '-'], '', $ytunnus);

        // Must be exactly 8 digits
        if (strlen($ytunnus) !== 8 || !ctype_digit($ytunnus)) {
            return false;
        }

        // Calculate checksum using Finnish algorithm
        $weights = [7, 9, 10, 5, 8, 4, 2];
        $sum = 0;

        for ($i = 0; $i < 7; $i++) {
            $sum += (int)$ytunnus[$i] * $weights[$i];
        }

        $remainder = $sum % 11;

        // If remainder is 1, the number is invalid
        if ($remainder === 1) {
            return false;
        }

        $expectedCheckDigit = $remainder === 0 ? 0 : 11 - $remainder;

        return $expectedCheckDigit === (int)$ytunnus[7];
    }
}