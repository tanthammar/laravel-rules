<?php

namespace TantHammar\LaravelRules\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

/**
 * Validates Estonian tax identification numbers:
 * - Registrikood: 8-digit business registry code. First digit:
 *     1 = commercial company, 7 = state/government/school,
 *     8 = non-profit association, 9 = foundation
 * - Isikukood: 11-digit personal identification code. First digit:
 *     1-8 = gender + century (1/2=1800s, 3/4=1900s, 5/6=2000s, 7/8=2100s)
 *
 * Accepts optional EE prefix. Both formats use the same mod-11 check digit algorithm.
 */
class EstonianBusinessId implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (blank($value)) {
            return false;
        }

        return $this->isValidEstonianId($value);
    }

    public function message(): string
    {
        return __('laravel-rules::messages.estonian-business-id');
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    private function isValidEstonianId(string $id): bool
    {
        $id = $this->normalize($id);

        return match (strlen($id)) {
            8 => ctype_digit($id) && $this->isValidRegistrikood($id),
            11 => ctype_digit($id) && $this->isValidIsikukood($id),
            default => false,
        };
    }

    /** True if the value is a valid 8-digit Estonian business registry code. */
    public function isBusinessCode(string $id): bool
    {
        $id = $this->normalize($id);
        return strlen($id) === 8 && ctype_digit($id) && $this->isValidRegistrikood($id);
    }

    /** True if the value is a valid 11-digit Estonian personal code (isikukood). */
    public function isPersonalCode(string $id): bool
    {
        $id = $this->normalize($id);
        return strlen($id) === 11 && ctype_digit($id) && $this->isValidIsikukood($id);
    }

    private function normalize(string $id): string
    {
        $id = trim($id);
        if (stripos($id, 'EE') === 0) {
            $id = substr($id, 2);
        }
        return str_replace([' ', '-'], '', $id);
    }

    /**
     * Business registry code: 8 digits, valid starting digit, mod-11 checksum.
     */
    private function isValidRegistrikood(string $id): bool
    {
        if (! in_array($id[0], ['1', '7', '8', '9'], true)) {
            return false;
        }

        return (int) $id[7] === $this->calcCheckDigit($id, 7);
    }

    /**
     * Personal code: 11 digits, valid century digit, valid birth date, mod-11 checksum.
     */
    private function isValidIsikukood(string $id): bool
    {
        $century = (int) $id[0];
        if ($century < 1 || $century > 8) {
            return false;
        }

        $centuryYear = match (true) {
            $century <= 2 => 1800,
            $century <= 4 => 1900,
            $century <= 6 => 2000,
            default => 2100,
        };

        $year = $centuryYear + (int) substr($id, 1, 2);
        $month = (int) substr($id, 3, 2);
        $day = (int) substr($id, 5, 2);

        if (! checkdate($month, $day, $year)) {
            return false;
        }

        return (int) $id[10] === $this->calcCheckDigit($id, 10);
    }

    /**
     * Isikukood-style mod-11 check digit over the first $length digits.
     * First pass weights: (i % 9) + 1; if result is 10, retry with ((i + 2) % 9) + 1;
     * check digit = result % 10.
     */
    private function calcCheckDigit(string $id, int $length): int
    {
        $sum = 0;
        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $id[$i] * (($i % 9) + 1);
        }
        $check = $sum % 11;

        if ($check === 10) {
            $sum = 0;
            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $id[$i] * ((($i + 2) % 9) + 1);
            }
            $check = $sum % 11;
        }

        return $check % 10;
    }
}